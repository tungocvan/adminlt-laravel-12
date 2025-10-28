<!DOCTYPE html>
<html>
<head>
  <title>Google Callback</title>
</head>
<body>
<script>
  // 1️⃣ Lấy token từ fragment URL
  const hash = window.location.hash.substring(1);
  const params = Object.fromEntries(new URLSearchParams(hash));
  const token = params.access_token;

  if (token) {
    // 2️⃣ Gửi token lên backend để tạo JWT / access_token nội bộ
    fetch('https://adminlt.tungocvan.com/api/google/callback', {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify({id_token: token})
    })
    .then(res => res.json())
    .then(data => {
      // 3️⃣ Khi thành công → redirect về app qua deep link
      window.location = `myapp://google-callback?token=${data.access_token}`;
    })
    .catch(err => {
      console.error(err);
      document.body.innerHTML = 'Đăng nhập thất bại!';
    });
  } else {
    document.body.innerHTML = 'Không lấy được token từ Google!';
  }
</script>
</body>
</html>
