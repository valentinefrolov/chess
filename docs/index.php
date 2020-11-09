<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Chess</title>
</head>
<body>
<div class="holder">
    <div id="Test"></div>
</div>
</body>
<script>
    window.wsHost = 'localhost:8080';
    window.initUrl = '/init.php';
</script>
<link rel="stylesheet" href="/css/index.css"/>
<script src="/js/app.bundle.js"></script>
</html>


<!--<script>
    var conn = new WebSocket('ws://localhost:8080');
    conn.onopen = function(e) {
        console.log("Connection established!");
    };

    conn.onmessage = function(e) {
        console.log(e.data);
    };
</script>
-->
