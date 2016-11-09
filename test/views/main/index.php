<head>
    <script id="digits-sdk" src="https://cdn.digits.com/1/sdk.js"></script>
    <script
        src="https://code.jquery.com/jquery-3.1.1.min.js"
        integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8="
        crossorigin="anonymous"></script>
</head>
<script type="text/javascript">

    Digits.init({ consumerKey: 'nf93c7wVm6qQZi8xIeheGoyEP' });
    Digits.embed({
        container: '.my-digits-container'
    })
        .done(function (loginResponse) {
            var oAuthHeaders = loginResponse.oauth_echo_headers;
            var verifyData = {
                authHeader: oAuthHeaders['X-Verify-Credentials-Authorization'],
                apiUrl: oAuthHeaders['X-Auth-Service-Provider']
            };
            console.log(loginResponse);
        }) /*handle the response*/
        .fail(onLoginFailure);


</script>

<div class="my-digits-container" id="container"></div>
