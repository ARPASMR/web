$(function(){
	// #### Ordinamento tabelle ####
        $("table").tablesorter({
            sortList: [[1,0]],
	    headers: {
		0:{sorter:false, filter: false}
		},
	    widgets: ["filter"]
        });
});