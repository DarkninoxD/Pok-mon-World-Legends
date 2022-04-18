<?php 
    $page = 'notfound';
    //Goeie taal erbij laden voor de page
    include_once('language/language-pages.php');

    echo '<div class="red">';
    echo $txt['notfoundtext']; 
    echo '</div>';
?>