<html>
<head>
<title>Device Locked</title>
<style>
    body { 
        margin:0;
        background:black;
        color:white;
        text-align:center;
        padding-top:20%;
        font-size:24px;
        font-family: 'Segoe UI', Arial, sans-serif;
        cursor: pointer;
    }
    .lock-icon { font-size: 80px; margin-bottom: 20px; }
    .blink { animation: blinker 1.5s linear infinite; color: #ff4444; }
    @keyframes blinker { 50% { opacity: 0; } }
</style>
</head>
<body onclick="unlock()">

<div class="lock-icon">🔒</div>
<p class="blink">SYSTEM LOCKED</p>
<p>This is a safe phishing simulation.</p>
<p><small>(Click anywhere to unlock and proceed to training)</small></p>

<script>
function unlock() {
    window.location = "/training";
}
</script>

</body>
</html>
