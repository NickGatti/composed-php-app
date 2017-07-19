<?php echo $this->render('head'); ?>
        <h1><?php echo $title; ?></h1>
        <div id="message"></div>
        <form id="inputForm" name="inputForm">
            <input type="text" id="name" name="name" value="<?php echo $name; ?>" placeholder="name" /><br />
            <input type="text" id="color" name="color" value="<?php echo $color; ?>" placeholder="color" /><br />
            <input type="text" id="image" name="image" value="<?php echo $image; ?>" placeholder="imageurl" /><br />
            <textarea name="description" placeholder="description"><?php echo $description; ?></textarea><br />
            <input type="checkbox" name="wearable" /><br />
            <input type="hidden" name="username" value="<?php echo $user->user; ?>" />
            <input type="hidden" name="token" value="<?php echo $user->token; ?>" />
            <input type="hidden" name="id" value="<?php echo $id; ?>" />
            <input type="submit" name="submit" value="submit" /><br />
        </form>
        <script>
            /* global fetch */
            var editing = <?php echo $editing; ?>;
            var data = document.forms.namedItem('inputForm');
            function editAnimal(e) {
                e.preventDefault();
                for (var entry of (new FormData(data)).entries())
                {
                    result[entry[0]] = entry[1];
                }
                result = JSON.stringify(result);
                
                fetch('/animals', {
                    headers: {
                        'x-requested-with': 'fetch'
                    },
                    method: 'PUT',
                    body: result
                }).then(function(response) {
                    return response.json();
                }).then(function(json) {
                    document.getElementById('message').innerHTML = json.message;
                })
            }
            
            function submitForm(e) {
                e.preventDefault();
                fetch('/animals', {
                    headers: {
                        'x-requested-with': 'fetch'
                    },
                    method: 'POST',
                    body: new FormData(data)
                }).then(function(response) {
                    return response.json();
                }).then(function(json) {
                    if(json.message === 'success') {
                        data.onsubmit = editAnimal;
                    }
                    
                    document.getElementById('message').innerHTML = json.message;
                });
            }
            
            if(!editing) {
                data.onsubmit = submitForm;
            } else {
                data.onsubmit = editAnimal;
            }
        </script>
        <hr />
        <a href="/logout">Log Out</a>
<?php echo $this->render('foot'); ?>