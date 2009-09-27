<?php
// echo error_get_last();
print 'HI!';
// print_r($_SERVER);

// echo error_get_last();
require('./lib/contextobject_class.php');

$stringtotest = 'ctx_ver=Z39.88-2004&rft_val_fmt=info%3Aofi%2Ffmt%3Akev%3Amtx%3Apatent&rfr_id=info%3Asid%2Focoins.info%3Agenerator&rft.title=Patent+Title&rft.number=Patent+Number&rft.invlast=Last+Name&rft.invfirst=First+Name&rft.assignee=Patent+Assignee&rft.cc=US&rft.date=2010-01-31&rft.applnumber=Application+Number&rft.appldate=2009-01-31&rft.applcc=NZ';

// new openurl($_SERVER['QUERY_STRING']);

//$openurl = new contextobject;
$openurl = new contextobject;
$openurl->build_from_string($stringtotest);
// echo error_get_last();
// $openurl->
// var_dump(new contextobject);
// var_dump($openurl->co);
print_r($openurl);

// echo error_get_last();
?>