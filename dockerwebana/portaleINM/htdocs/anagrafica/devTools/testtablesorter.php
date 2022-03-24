<!DOCTYPE html>
<html>

    <head>
        <title>Archivio Misure Meteorologiche @ Sinergico</title>
        <meta charset=ISO-8859-1>
        <!-- librerie esterne (in locale) -->
		<script type="text/javascript" src="../resources/external/jquery-3.6.0.min.js"></script>
		<script type="text/javascript" src="../resources/external/jquery-ui-1.13.1/jquery-ui.min.js"></script>
        <script type="text/javascript" src="../resources/external/tablesorter/dist/js/jquery.tablesorter.js" ></script>
        <script type="text/javascript" src="../resources/external/tablesorter/dist/js/widgets/widget-filter.min.js" ></script>
        <script type="text/javascript" src="../resources/external/tablesorter/dist/js/widgets/widget-storage.min.js" ></script>
        <script type="text/javascript" src="../resources/external/jquery.validate.-1.11.1.min.js" ></script>
        <script type="text/javascript" src="../resources/external/messages_it.js" ></script>
        <script type="text/javascript" src="../resources/external/jquery-migrate-3.3.2.min.js"></script>
        <!-- fogli di stile -->
        <link rel="stylesheet" href="../resources/external/jquery-ui-1.13.1/jquery-ui.min.css">
        <link rel="stylesheet" href="../resources/external/tablesorter/dist/css/theme.blue.css">
        <!-- javascript -->
        <script type="text/javascript" src="../resources/js/input.js" ></script>
        <script type="text/javascript" src="../resources/js/anagrafica.js" ></script>
        
        <!-- Tablesorter: required -->
    	<!-- <link rel="stylesheet" href="../css/theme.blue.css">
    	<script src="../js/jquery.tablesorter.js"></script>
    	<script src="../js/widgets/widget-storage.js"></script>
    	<script src="../js/widgets/widget-filter.js"></script>-->
        <script>
			$(function() {
        
            	// call the tablesorter plugin
            	$("#mytable").tablesorter({
            		//theme: 'blue',
            
            		// hidden filter input/selects will resize the columns, so try to minimize the change
            		widthFixed : true,
            
            		// initialize zebra striping and filter widgets
            		widgets: ["zebra", "filter"],
            
            		// headers: { 5: { sorter: false, filter: false } },
            
            		widgetOptions : {
            
            			// extra css class applied to the table row containing the filters & the inputs within that row
            			filter_cssFilter   : '',
            
            			// If there are child rows in the table (rows with class name from "cssChildRow" option)
            			// and this option is true and a match is found anywhere in the child row, then it will make that row
            			// visible; default is false
            			filter_childRows   : false,
            
            			// if true, filters are collapsed initially, but can be revealed by hovering over the grey bar immediately
            			// below the header row. Additionally, tabbing through the document will open the filter row when an input gets focus
            			filter_hideFilters : false,
            
            			// Set this option to false to make the searches case sensitive
            			filter_ignoreCase  : true,
            
            			// jQuery selector string of an element used to reset the filters
            			filter_reset : '.reset',
            
            			// Use the $.tablesorter.storage utility to save the most recent filters
            			filter_saveFilters : true,
            
            			// Delay in milliseconds before the filter widget starts searching; This option prevents searching for
            			// every character while typing and should make searching large tables faster.
            			filter_searchDelay : 300,
            
            			// Set this option to true to use the filter to find text from the start of the column
            			// So typing in "a" will find "albert" but not "frank", both have a's; default is false
            			filter_startsWith  : false,
            
            			// Add select box to 4th column (zero-based index)
            			// each option has an associated function that returns a boolean
            			// function variables:
            			// e = exact text from cell
            			// n = normalized value returned by the column parser
            			// f = search filter input value
            			// i = column index
            			filter_functions : {
            
            				// Add select menu to this column
            				// set the column value to true, and/or add "filter-select" class name to header
            				// '.first-name' : true,
            
            				// Exact match only
            				1 : function(e, n, f, i, $r, c, data) {
            					return e === f;
            				},
            
            				// Add these options to the select dropdown (regex example)
            				2 : {
            					"A - D" : function(e, n, f, i, $r, c, data) { return /^[A-D]/.test(e); },
            					"E - H" : function(e, n, f, i, $r, c, data) { return /^[E-H]/.test(e); },
            					"I - L" : function(e, n, f, i, $r, c, data) { return /^[I-L]/.test(e); },
            					"M - P" : function(e, n, f, i, $r, c, data) { return /^[M-P]/.test(e); },
            					"Q - T" : function(e, n, f, i, $r, c, data) { return /^[Q-T]/.test(e); },
            					"U - X" : function(e, n, f, i, $r, c, data) { return /^[U-X]/.test(e); },
            					"Y - Z" : function(e, n, f, i, $r, c, data) { return /^[Y-Z]/.test(e); }
            				},
            
            				// Add these options to the select dropdown (numerical comparison example)
            				// Note that only the normalized (n) value will contain numerical data
            				// If you use the exact text, you'll need to parse it (parseFloat or parseInt)
            				4 : {
            					"< $10"      : function(e, n, f, i, $r, c, data) { return n < 10; },
            					"$10 - $100" : function(e, n, f, i, $r, c, data) { return n >= 10 && n <=100; },
            					"> $100"     : function(e, n, f, i, $r, c, data) { return n > 100; }
            				}
            			}
            
            		}
            
            	});
        
        	});
        </script>
		
		
    </head>

    <body>
		                
                <table id="mytable" class="tablesorter">
                  <thead>
                    <tr>
                      <!-- add "filter-select" class or filter_functions : { '.first-name' : true } -->
                      <!-- add "filter-match" class to just match the content, so selecting "Denni" will also show "Dennis" -->
                      <th class="first-name filter-select" data-placeholder="Select a name">First Name</th>
                      <th data-placeholder="Exact matches only">Last Name</th>
                      <th data-placeholder="Choose a city">City</th>
                      <th data-value="<30">Age</th>
                      <th data-placeholder="Select a filter">Total</th>
                      <th class="filter-select filter-onlyAvail">Discount</th>
                      <th>Date</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>Aaron</td>
                      <td>Johnson Sr</td>
                      <td>Atlanta</td>
                      <td>35</td>
                      <td>$5.95</td>
                      <td>22%</td>
                      <td>Jun 26, 2004 7:22 AM</td>
                    </tr>
                    <tr>
                      <td>Aaron</td>
                      <td>Johnson</td>
                      <td>Yuma</td>
                      <td>12</td>
                      <td>$2.99</td>
                      <td>5%</td>
                      <td>Aug 21, 2009 12:21 PM</td>
                    </tr>
                    <tr>
                      <td>Clark</td>
                      <td>Henry Jr</td>
                      <td>Tampa</td>
                      <td>51</td>
                      <td>$42.29</td>
                      <td>18%</td>
                      <td>Oct 13, 2000 1:15 PM</td>
                    </tr>
                    <tr>
                      <td>Denni</td>
                      <td>Henry</td>
                      <td>New York</td>
                      <td>28</td>
                      <td>$9.99</td>
                      <td>20%</td>
                      <td>Jul 6, 2006 8:14 AM</td>
                    </tr>
                    <tr>
                      <td>John</td>
                      <td>Hood</td>
                      <td>Boston</td>
                      <td>33</td>
                      <td>$19.99</td>
                      <td>25%</td>
                      <td>Dec 10, 2002 5:14 AM</td>
                    </tr>
                    <tr>
                      <td>Clark</td>
                      <td>Kent Sr</td>
                      <td>Los Angeles</td>
                      <td>18</td>
                      <td>$15.89</td>
                      <td>44%</td>
                      <td>Jan 12, 2003 11:14 AM</td>
                    </tr>
                    <tr>
                      <td>Peter</td>
                      <td>Kent Esq</td>
                      <td>Seattle</td>
                      <td>45</td>
                      <td>$153.19</td>
                      <td>44%</td>
                      <td>Jan 18, 2021 9:12 AM</td>
                    </tr>
                    <tr>
                      <td>Peter</td>
                      <td>Johns</td>
                      <td>Milwaukee</td>
                      <td>13</td>
                      <td>$5.29</td>
                      <td>4%</td>
                      <td>Jan 8, 2012 5:11 PM</td>
                    </tr>
                    <tr>
                      <td>Aaron</td>
                      <td>Evan</td>
                      <td>Chicago</td>
                      <td>24</td>
                      <td>$14.19</td>
                      <td>14%</td>
                      <td>Jan 14, 2004 11:23 AM</td>
                    </tr>
                    <tr>
                      <td>Bruce</td>
                      <td>Evans</td>
                      <td>Upland</td>
                      <td>22</td>
                      <td>$13.19</td>
                      <td>11%</td>
                      <td>Jan 18, 2007 9:12 AM</td>
                    </tr>
                    <tr>
                      <td>Clark</td>
                      <td>McMasters</td>
                      <td>Pheonix</td>
                      <td>18</td>
                      <td>$55.20</td>
                      <td>15%</td>
                      <td>Feb 12, 2010 7:23 PM</td>
                    </tr>
                    <tr>
                      <td>Dennis</td>
                      <td>Masters</td>
                      <td>Indianapolis</td>
                      <td>65</td>
                      <td>$123.00</td>
                      <td>32%</td>
                      <td>Jan 20, 2001 1:12 PM</td>
                    </tr>
                    <tr>
                      <td>John</td>
                      <td>Hood</td>
                      <td>Fort Worth</td>
                      <td>25</td>
                      <td>$22.09</td>
                      <td>17%</td>
                      <td>Jun 11, 2011 10:55 AM</td>
                    </tr>
                  </tbody>
                </table>
                
                
	</body>
</html>