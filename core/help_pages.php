<?php

require_once("template/text.php");

if(isset($_GET['regarding'])){      
/*<?php   case "": ?/>

<?php   break; ?/>    */ 
    switch($_GET['regarding']){  
        default:
             
        break; ?>
<?php   case "is_editable": ?>

<?php   break; ?>
<?php   case "dt_search": ?>
<h4>Database Searching</h4>
<h5>Search Query Condition:</h5>
<p>Extra set of conditions on your desired search query. There are three possible options, namely "Begins With", "Ends with", and "Excludes the following". The <strong>"Begins With"</strong> clause will search any data whose value starts with your search query. The <strong>"Ends With"</strong> clause will search any data whose value ends with your search query Lastly, the <strong>"Excludes the following"</strong> clause will exclude any data whose value contains with your search query.</p>
<h5>Search on Column:</h5> 
<p>If any option but the default one is selected, data will be searched in the specific column where you looked for data.</p>

<h5>Order by Column:</h5>
<p>If any option but the default one is selected, the searched data will be ordered by your <strong>Order of Choice</strong>(the next selection) on the column where you selected the data.</p>
<?php   break; ?>

<?php }} ?>