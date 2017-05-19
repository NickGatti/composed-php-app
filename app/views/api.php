<html>
    <head>
        
    </head>
    <body>
        
    </body>
    
    <div>
        <h1>Loading Api..</h1>
        <br>
        
        <p id='api_output'></p>
        
        <br>
        <h1>***</h1>
    </div>
    
    <script>
        getAPI();
    
        function getAPI () {
            
            fetch('https://php-new-newwork-nickgatti.c9users.io/api', {method: 'GET'}).then(function(data) {
                return data.json();
            }).then(function(data) {
                document.getElementById('api_output').innerHTML = data.message;
            });
            
        }
    </script>
    
</html>