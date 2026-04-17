<!DOCTYPE html>
<html lang="ar">
<head>
<meta charset="UTF-8">
<style>
body {
    margin:0;
    width:1200px;
    height:630px;
    font-family:Arial;
    background:url('{{ $imageUrl }}') center/cover no-repeat;
}
.overlay {
    position:absolute;
    bottom:0;
    width:100%;
    height:230px;
    background:linear-gradient(to top, rgba(0,0,0,0.9), transparent);
}
.title {
    position:absolute;
    bottom:100px;
    width:100%;
    text-align:center;
    font-size:60px;
    color:white;
    font-weight:bold;
}
.site {
    position:absolute;
    bottom:30px;
    width:100%;
    text-align:center;
    color:#cbd5f5;
}
.badge {
    position:absolute;
    top:40px;
    left:40px;
    background:#ef4444;
    padding:10px 20px;
    color:white;
    border-radius:8px;
}
</style>
</head>
<body>

<div class="badge">عاجل</div>
<div class="overlay"></div>
<div class="title">{{ $title }}</div>
<div class="site">thealawites.com</div>

</body>
</html>