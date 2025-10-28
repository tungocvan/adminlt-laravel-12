<!DOCTYPE html>
<html>
<head>
  <title>Google Login</title>
</head>
<body>
<script>
  // 1️⃣ Lấy access_token từ fragment
  const hash = window.location.hash.substr(1);
  const params = Object.fromEntries(new URLSearchParams(hash));
  const token = params.access_token;

  if (!token) {
    document.body.innerHTML = "No token found!";
  } else {
    // 2️⃣ Gửi token lên backend để xác thực và tạo user
    fetch('https://adminlt.tungocvan.com/api/google/callback', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id_token: token })
    })
    .then(res => res.json())
    .then(data => {
      console.log('✅ Backend response:', data);
      // 3️⃣ Redirect về app bằng deep link
      window.location = `myapp://google-callback?token=${token}`;
    })
    .catch(err => {
      console.error(err);
      document.body.innerHTML = "Error sending token to backend!";
    });
  }
</script>
</body>
</html>
