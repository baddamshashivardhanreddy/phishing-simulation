<!DOCTYPE html>
<html>
<head>
    <title>Quiz Result</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style> body { text-align:center; padding-top: 50px; background-color: #f4f6f9; } </style>
</head>
<body>

<div class="container">
    <div class="card shadow-sm mx-auto" style="max-width: 500px; padding: 30px;">
        <?php if($passed): ?>
            <h1 class="text-success display-1">✔</h1>
            <h2 class="text-success">You Passed!</h2>
            <p class="lead">Great job spotting the signs of phishing.</p>
        <?php else: ?>
            <h1 class="text-danger display-1">✘</h1>
            <h2 class="text-danger">Try Again</h2>
            <p class="lead">You missed some questions. Please review the training material.</p>
        <?php endif; ?>

        <hr>
        <h4>Score: <?= $score ?> / <?= $total ?></h4>
        
        <br>
        <?php if($passed): ?>
             <a href="/" class="btn btn-outline-primary">Return Home</a>
        <?php else: ?>
             <a href="/quiz" class="btn btn-warning">Retake Quiz</a>
             <a href="/training" class="btn btn-secondary">Review Training</a>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
