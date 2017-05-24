<?php echo $this->render('head'); ?>
        <h1><?php echo $title; ?></h1>
        <form id='inputForm'>
            <input type="text" name="name" placeholder="name" /><br />
            <input type="text" name="color" placeholder="color" /><br />
            <input type="text" name="username" placeholder="username" /><br />
            <input type="text" name="image" placeholder="image url" /><br />
            <textarea name="description"></textarea><br />
            <input type="checkbox" name="wearable" />
            <input type="submit" name="submit" value="submit" />
        </form>
        <hr>
        <script>
            var data = document.querySelector('#inputForm');
            data.onsubmit = function(e) {
                e.preventDefault();
                fetch('/animals', {
                    method: 'POST',
                    data: new FormData(data)
                });
            }
        </script>
        <a href="/logout">Log Out</a>
<?php echo $this->render('foot'); ?>