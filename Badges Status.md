<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Badges</title>

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Inter, Arial, sans-serif;
}

body{
    background:#0d1117;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
    color:white;
}

.card{
    width:500px;
    background:#161b22;
    border:1px solid #30363d;
    border-radius:18px;
    padding:45px;
    text-align:center;
    box-shadow:0 20px 50px rgba(0,0,0,.4);
}

.icon{
    width:90px;
    height:90px;
    margin:0 auto 25px;
    border-radius:50%;
    background:#21262d;
    display:flex;
    justify-content:center;
    align-items:center;
    font-size:42px;
}

h1{
    font-size:32px;
    margin-bottom:15px;
}

p{
    color:#8b949e;
    line-height:1.7;
    margin-bottom:30px;
}

.notice{
    background:#1f6feb20;
    border:1px solid #1f6feb;
    border-radius:10px;
    padding:16px;
    color:#c9d1d9;
}

button{
    margin-top:30px;
    padding:12px 28px;
    background:#238636;
    color:white;
    border:none;
    border-radius:8px;
    font-size:15px;
    cursor:pointer;
    transition:.2s;
}

button:hover{
    background:#2ea043;
}
</style>

</head>
<body>

<div class="card">

<div class="icon">🏅</div>

<h1>Badges Unavailable</h1>

<p>
The badge system is currently unavailable while we make improvements.
We're working hard to bring it back soon.
</p>

<div class="notice">
<strong>Status:</strong> Temporarily Disabled
</div>

<button onclick="history.back()">Go Back</button>

</div>

</body>
</html>
