# 🚀 PhishCall - AWS EC2 & RDS Production Deployment Guide

This guide walks you through deploying **PhishCall** in production on **Amazon Web Services (AWS)** using **AWS EC2 (Docker)** and **AWS RDS (MySQL)**, utilizing an isolated dual-subdomain architecture.

---

## 🏗️ AWS Cloud Architecture

```mermaid
graph TD
    subgraph Internet["Public Internet & Employees"]
        TargetUser[Target Employee] -->|Clicks Email Link| PhishDomain["rewards.yourcompany.com"]
        CuriousUser[Target or External Scanner] -->|Visits /login, /admin, or /| PhishDomain
        AdminUser[SecOps Admin] -->|Direct Access| AdminDomain["admin.yourcompany.com"]
    end

    subgraph AWS["AWS Cloud VPC"]
        subgraph EC2["EC2 Instance (t4g.small)"]
            Docker["Docker Container (Nginx + PHP 8.2-FPM)"]
            PhishDomain -->|Port 80/443| Docker
            AdminDomain -->|Port 80/443| Docker
            Docker --> Decoy404["Stealth 404 Not Found (Cloaked)"]
            Docker --> AdminPortal["Admin Dashboard & Login"]
            Docker --> PhishFlow["/download, /training, Form & Debrief"]
        end

        subgraph RDS["AWS RDS (MySQL 8.0)"]
            Docker -->|Port 3306 (VPC Internal)| Database[(phishing_simulation DB)]
        end
    end
```

---

## 📋 Recommended AWS Sizing

| Component | AWS Resource | Recommended Size | Notes |
|---|---|---|---|
| **Web & App Server** | **AWS EC2** | **`t4g.small`** (ARM Graviton2) or `t3.small` | 2 vCPU / 2 GB RAM. Fast, handles blast click surges. |
| **Database** | **AWS RDS (MySQL)** | **`db.t4g.micro`** or `db.t3.micro` | Eligible for AWS Free Tier. 20 GB gp3 storage. |
| **OS** | **Amazon Linux 2023** or **Ubuntu 22.04 LTS** | 64-bit ARM or x86 | Minimal, secure, optimized for Docker. |

---

## 🛡️ Step 1: AWS Security Groups Setup

Before launching, configure two Security Groups in your AWS VPC:

### 1. EC2 Security Group (`sg-phishcall-ec2`)
* **Inbound Rules**:
  * `HTTP` (Port 80) from `0.0.0.0/0`
  * `HTTPS` (Port 443) from `0.0.0.0/0`
  * `SSH` (Port 22) restricted to **Your Office/VPN IP only**

### 2. RDS Security Group (`sg-phishcall-rds`)
* **Inbound Rules**:
  * `MYSQL/Aurora` (Port 3306) restricted to **Source: `sg-phishcall-ec2`** *(Never open to `0.0.0.0/0`)*

---

## 🗄️ Step 2: AWS RDS Database Setup & Schema Import

1. Create a MySQL 8.0 instance in AWS RDS.
2. Note your **RDS Endpoint** (e.g. `phishcall-db.cxxxxxxx.us-east-1.rds.amazonaws.com`).
3. Connect from your EC2 instance (or local machine with temporary access) and import the schema:

```bash
# Connect to RDS and create the database
mysql -h phishcall-db.cxxxxxxx.us-east-1.rds.amazonaws.com -u admin -p -e "CREATE DATABASE phishing_simulation CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Import the initial schema and templates
mysql -h phishcall-db.cxxxxxxx.us-east-1.rds.amazonaws.com -u admin -p phishing_simulation < database.sql
```

---

## 🌐 Step 3: Configure DNS Records

In your DNS provider (Cloudflare, AWS Route 53, or Domain Registrar), create two `A` records pointing to your **EC2 Public Elastic IP**:

| Record Type | Host / Name | Target | Purpose |
|---|---|---|---|
| `A` | `admin` (e.g. `admin.yourcompany.com`) | `EC2_PUBLIC_IP` | Restricted Admin Portal |
| `A` | `rewards` (e.g. `rewards.yourcompany.com`) | `EC2_PUBLIC_IP` | Decoy Phishing Link sent to users |

---

## 🐳 Step 4: Docker Deployment on EC2 (Recommended)

### 1. SSH into your EC2 Instance and Install Docker
**For Amazon Linux 2023:**
```bash
sudo dnf update -y
sudo dnf install -y docker git
sudo systemctl enable --now docker
sudo usermod -aG docker $USER

# Install Docker Compose Plugin
DOCKER_CONFIG=${DOCKER_CONFIG:-$HOME/.docker}
mkdir -p $DOCKER_CONFIG/cli-plugins
curl -SL https://github.com/docker/compose/releases/latest/download/docker-compose-linux-$(uname -m) -o $DOCKER_CONFIG/cli-plugins/docker-compose
chmod +x $DOCKER_CONFIG/cli-plugins/docker-compose

# Re-login to apply group permissions
newgrp docker
```

**For Ubuntu 22.04 / 24.04:**
```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y docker.io docker-compose-v2 git
sudo systemctl enable --now docker
sudo usermod -aG docker $USER
newgrp docker
```

---

### 2. Clone Repository & Setup Environment
```bash
git clone <your-repo-url> /opt/phishcall
cd /opt/phishcall
```

Set your production parameters in `docker-compose.yml` (or `.env`):
```yaml
    environment:
      # AWS RDS MySQL Configuration
      - DB_HOST=phishcall-db.cxxxxxxx.us-east-1.rds.amazonaws.com
      - DB_DATABASE=phishing_simulation
      - DB_USER=admin
      - DB_PASS=YourStrongRdsPassword
      - DB_PORT=3306

      # Cloaked Subdomains
      - APP_BASE_URL=http://admin.yourcompany.com/
      - ADMIN_SERVER_URL=http://admin.yourcompany.com
      - PHISH_SERVER_URL=http://rewards.yourcompany.com
      - CI_ENVIRONMENT=production
```
*(Alternatively, you can also copy `.env.example` to `.env` and configure it there).*

---

### 3. Build & Run the Container
```bash
docker compose up -d --build
```

Check container status and logs:
```bash
docker compose ps
docker compose logs -f
```

---

## 🔒 Step 5: HTTPS / SSL Setup (Free Let's Encrypt or AWS ALB)

### Option A: AWS Application Load Balancer (ALB) - Easiest & Best for AWS
1. In AWS EC2 Console, create an **Application Load Balancer**.
2. Request a free SSL certificate in **AWS Certificate Manager (ACM)** for `admin.yourcompany.com` and `rewards.yourcompany.com`.
3. Configure ALB listener:
   - HTTPS:443 -> Forward to EC2 instance on Port 80.
   - HTTP:80 -> Redirect to HTTPS:443.

### Option B: Let's Encrypt Certbot via Host Reverse Proxy
Install Certbot on the host:
```bash
sudo certbot certonly --standalone -d admin.yourcompany.com -d rewards.yourcompany.com
```

---

## 🔑 Step 6: Initial Login & Setup

1. Open your browser and navigate to:
   `http://admin.yourcompany.com/login` (or `https://...`)

2. Default administrator credentials:
   - **Username**: `admin`
   - **Password**: `admin` *(Change immediately in **Admin Users** (`/admin/profile`))*

3. Navigate to **Settings**:
   - Verify **Admin Server URL** and **Phishing Server URL**.
   - Configure your **SMTP / Brevo API Key** to enable live email delivery.
   - Set **Sender Display Name** (`Ravindranath Yarlagadda`) and sender email.

---

## ✅ Step 7: Verification & Security Testing

Test all boundaries to ensure complete domain separation:

| Test Scenario | Action / URL | Expected Result |
|---|---|---|
| **Admin Portal** | Open `https://admin.yourcompany.com/login` | ✅ Displays PhishCall Admin Login |
| **Phishing Decoy Root** | Open `https://rewards.yourcompany.com/` | 🛡️ Returns **404 Not Found** (Authentic Apache Decoy) |
| **Phishing Decoy Admin** | Open `https://rewards.yourcompany.com/admin` | 🛡️ Returns **404 Not Found** (Strictly Cloaked) |
| **Phishing Decoy Login** | Open `https://rewards.yourcompany.com/login` | 🛡️ Returns **404 Not Found** (Strictly Cloaked) |
| **Simulation Endpoint** | Open `https://rewards.yourcompany.com/training` | ✅ Displays Microsoft Forms awareness simulation |

---

## 🛠️ Docker Maintenance Commands

```bash
# Restart container
docker compose restart

# View live application & access logs
docker compose logs -f phishcall

# Update application code
git pull
docker compose up -d --build

# Run database migrations or spark commands inside container
docker compose exec phishcall php spark
```
