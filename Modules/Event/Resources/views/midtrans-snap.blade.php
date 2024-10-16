<html>

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- @TODO: replace SET_YOUR_CLIENT_KEY_HERE with your client key -->
  <script type="text/javascript"
		src="https://app.sandbox.midtrans.com/snap/snap.js"
    data-client-key="SB-Mid-client-X40mVZva7BgC8YRY"></script>
  <!-- Note: replace with src="https://app.midtrans.com/snap/snap.js" for Production environment -->
</head>

<body>
  <!-- @TODO: You can add the desired ID as a reference for the embedId parameter. -->
  <div id="snap-container"></div>

  <script type="text/javascript">
    var token = @json(request()->route('snapToken'));
    document.addEventListener("DOMContentLoaded", function() {
        window.snap.pay(token, {
          onSuccess: function(result){
            parent.postMessage('success', '*');
          },
          onPending: function(result){
            parent.postMessage('pending', '*');
          },
          onError: function(result){
            parent.postMessage('failed', '*');
          },
          onClose: function(){
            /* You may add your own implementation here */
            parent.postMessage('close', '*');
          }
        })
    });
  </script>
</body>

</html>