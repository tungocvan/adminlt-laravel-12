<!DOCTYPE html>
<html>
<head>
  <title>Google OAuth Callback</title>
</head>
<body>
<script>
  // 1️⃣ Lấy access_token từ fragment
  const hash = window.location.hash.substring(1); // bỏ #
  const params = Object.fromEntries(new URLSearchParams(hash));
  const token = params.access_token;

  // 2️⃣ Gửi token về React Native bằng postMessage
  if (window.ReactNativeWebView) {
    // nếu chạy trong WebView, gửi về RN
    window.ReactNativeWebView.postMessage(JSON.stringify({ token }));
  } else {
    // Nếu dùng Expo WebBrowser.openAuthSessionAsync
    // chrome custom tabs / safari view controller sẽ detect URL scheme
    const urlScheme = new URLSearchParams({ token });
    window.location = `myapp://oauth2redirect?${urlScheme.toString()}`;
  }
</script>
</body>
</html>
