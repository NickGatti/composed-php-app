<?php echo $this->render('head'); ?>
<?php if(!empty($error)): ?>
    <div class="error"><?php echo $error; ?></div>
<?php endif; ?>
    <form method="POST">
        <input name='username' value="<?php echo $username ?>" type='text' placeholder='username' />
        <input name='password' type='password' placeholder='<?php echo $password; ?>' />
<?php if($register): ?>
        <input name='email' type='text' placeholder='email' value='<?php echo $email ?>' />
<?php endif; ?>
        <input type='submit' value='<?php echo $button; ?>' />
    </form>
    <a href="<?php echo $register ? '/login' : '/create'; ?>">
        <?php echo $register ? 'Already have an account? Log in.' : 'Create an account'; ?>
    </a>
<?php echo $this->render('foot'); ?>