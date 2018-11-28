{$this.title=$name}
<div class="site-error">

    <h1>{$this.title|escape}</h1>

    <div class="alert alert-danger">
        {$message|escape|nl2br}
    </div>

    <p>
        The above error occurred while the Web server was processing your request.
    </p>
    <p>
        Please contact us if you think this is a server error. Thank you.
    </p>

</div>
