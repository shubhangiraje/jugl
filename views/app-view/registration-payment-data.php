<form action="https://checkout.wirecard.com/page/init.php" method="post" auto-submit>
    <input ng-repeat="param in formParams" type="hidden" name="{{::param.name}}" value="{{::param.value}}"/>
    <!--<input type="submit" value="submit">-->
</form>
