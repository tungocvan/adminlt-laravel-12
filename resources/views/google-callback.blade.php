<script>
  // Lấy token từ fragment
  const hash = window.location.hash.substr(1); // bỏ #
  const params = Object.fromEntries(new URLSearchParams(hash));
  const token = params.access_token;

  // Gửi token lên backend
  fetch('https://adminlt.tungocvan.com/api/google/callback', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id_token: token })
  }).then(res => res.json())
    .then(data => {
      // chuyển hướng về app / dashboard
      window.location = '/'; 
    });
</script>
