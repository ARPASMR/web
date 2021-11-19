<!DOCTYPE html>
<html>

    <head>
        <title>Archivio Misure Meteorologiche @ Sinergico</title>
        <meta charset=ISO-8859-1>
        <!-- librerie esterne (in locale) -->
		<script language="javascript" type="text/javascript" src="../resources/external/jquery-1.12.4.min.js"></script>
		<script language="javascript" type="text/javascript" src="../resources/external/jquery-ui.min.js"></script>
        <script language="javascript" type="text/javascript" src="../resources/external/jquery.tablesorter-2.15.13.min.js" ></script>
        <script language="javascript" type="text/javascript" src="../resources/external/jquery.tablesorter.widgets-2.15.13.min.js" ></script>
        <script language="javascript" type="text/javascript" src="../resources/external/jquery.tablesorter.pager.js"></script>
        <script language="javascript" type="text/javascript" src="../resources/external/jquery.validate.-1.11.1.min.js" ></script>
        <script language="javascript" type="text/javascript" src="../resources/external/messages_it.js" ></script>
        <!-- fogli di stile -->
        <link rel="stylesheet" href="../resources/external/jquery-ui.min.css">
        <link rel="stylesheet" type="text/css" href="../resources/stile.css"  />
        <link rel="stylesheet" type="text/css" href="../resources/stile.layout.css"  />
        <link rel="stylesheet" type="text/css" href="../resources/stile.tablesorter.css"  />
        <!-- javascript -->
        <script language="javascript" type="text/javascript" src="../resources/js/input.js" ></script>
        <script language="javascript" type="text/javascript" src="../resources/js/anagrafica.js" ></script>
        <script type="text/javascript">
		$(function() {
			$("#mytable")
				.tablesorter({widthFixed: true, widgets: ['zebra', 'filter']})
				.tablesorterPager({
					container: $("#pager"), 
					output: '{startRow} - {endRow} / {filteredRows} ({totalRows})', 
					fixedHeight: true,
					removeRows: false
				});
		});
		</script>
    </head>

    <body>
		<script>
		/*$(document).ready(function() {

			var $table = $('table'),
			// define pager options
			pagerOptions = {
			    // target the pager markup - see the HTML block below
			    container: $(".pager"),
			    // output string - default is '{page}/{totalPages}';
			    // possible variables: {size}, {page}, {totalPages}, {filteredPages}, {startRow}, {endRow}, {filteredRows} and {totalRows}
			    // also {page:input} & {startRow:input} will add a modifiable input in place of the value
			    output: '{startRow} - {endRow} / {filteredRows} ({totalRows})',
			    // if true, the table will remain the same height no matter how many records are displayed. The space is made up by an empty
			    // table row set to a height to compensate; default is false
			    fixedHeight: true,
			    // remove rows from the table to speed up the sort of large tables.
			    // setting this to false, only hides the non-visible rows; needed if you plan to add/remove rows with the pager enabled.
			    removeRows: false,
			    // go to page selector - select dropdown that sets the current page
			    cssGoto: '.gotoPage'
			};

			$table.tablesorter({
				theme: 'blue',
				headerTemplate: '{content} {icon}',
				widthFix: true,
				widget: ['zebra', 'filter']
			})
			
			.tablesorterPager(pagerOptions);
			
			// // extend the default setting to always include the zebra widget. 
		    //$.tablesorter.defaults.widgets = ['zebra', 'filter']; 
		    //$.tablesorter.defaults.widthFixed = true;
		    //// extend the default setting to always sort on the first column 
		    //$.tablesorter.defaults.sortList = [[0,0]];
		    //$.tablesorter.tablesorterPager(pagerOptions);
		    //// call the tablesorter plugin 
		    //$("#mytable").tablesorter({ 
		    //    // pass the headers argument and assing a object 
		    //    headers: { 
		    //        // assign the secound column (we start counting zero) 
		    //        1: { 
		    //            // disable it by setting the property sorter to false 
		    //            sorter: false 
		    //        }, 
		    //       // assign the third column (we start counting zero) 
		    //        2: { 
		    //            // disable it by setting the property sorter to false 
		    //            sorter: false 
		    //        } 
		    //    } 
		    //});
		}); */
		/*$(document).ready(function() { 
		    $("#mytable") 
		    .tablesorter({widthFixed: true, widgets: ['zebra']}) 
		    .tablesorterPager({container: $("#pager")}); 
		});*/
		/* 1) $(document).ready(function() 
			    { 
			        $("#mytable").tablesorter(); 
			    } 
			);*/
			$(document).ready(function() 
				    { 
				        $("#mytable").tablesorter( {sortList: [[0,0], [1,0]]} ); 
				    } 
				);   
		</script>
		
		<table id="container">
            <tr>

                <td id="leftcolumn">
                    <div id="menu-container">
                        <?php //require_once("../menu.php"); ?>
                    </div>
                </td>

                <td id="rightcolumn">
                
                <table id="mytable" class="tablesorter">
                  <thead>
                    <tr>
                      <th>Column 1</th>
                      <th>Column 2</th>
                      <th>Column 3 - Column long column name</th>
                      <th>Column 4</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php
                  	for( $i = 0; $i < 22; $i++ )
                  	print (
                    '<tr> 
					    <td>Smith</td> 
					    <td>John</td> 
					    <td>jsmith@gmail.com</td> 
					    <td>$50.00</td> 
					</tr> 
					<tr> 
					    <td>Bach</td> 
					    <td>Frank</td> 
					    <td>fbach@yahoo.com</td> 
					    <td>$50.00</td>  
					</tr> 
					<tr> 
					    <td>Doe</td> 
					    <td>Jason</td> 
					    <td>jdoe@hotmail.com</td> 
					    <td>$100.00</td>  
					</tr> 
					<tr> 
					    <td>Conway</td> 
					    <td>Tim</td> 
					    <td>tconway@earthlink.net</td> 
					    <td>$50.00</td> 
					</tr>');
					?>
                  </tbody>
                </table>
                <div id="pager" class="pager tablesorter-pager">
					<form>
						<img src="./img/bullet.png" class="first" title="first"/>
						<img src="./img/bullet.png" class="prev" title="prev"/>
						<input type="text" class="pagedisplay"/>
						<img src="./img/bullet.png" class="next" title="next"/>
						<img src="./img/bullet.png" class="last" title="last"/>
						<select class="pagesize">
							<option selected="selected"  value="10">10</option>
							<option value="20">20</option>
							<option value="30">25</option>
							<option  value="40">50</option>
						</select>
					</form>
				</div>

<?php
  session_start();
  require_once("../__init__.php");
  

  
  
  
  
  
  
  
  
  require_once("../footer.php");

?>