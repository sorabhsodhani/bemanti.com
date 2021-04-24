/**
* @file
*/

(function ($, Drupal) {
  Drupal.behaviors.BemantiDatatablesInit = {
    attach: function (context, settings) {
      $('#bemanti-monthly-leaderboard tr th').eq(0).addClass('no-sort');
      var show_info = $( "#bemanti-monthly-leaderboard" ).hasClass( "no-entry-info" );
      table = $('.init-datatables').dataTable( {
        "pageLength": 25,
        "lengthChange": false,
        "searching": false,
        "destroy": true,
        "columnDefs": [ {
            "targets": "no-sort",
            "orderable": false
        } ],
        "info": $("#bemanti-monthly-leaderboard").hasClass("no-entry-info") ? false : true, 
        "order": [[ 2, "desc" ]],
       fnRowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {  
            //debugger;  
            var index = (iDisplayIndexFull + 1);  
            $("td:first", nRow).html(index);  
            return nRow;  
        },  
        fnDrawCallback: function(oSettings) {
          var totalPages = this.api().page.info().pages;
          if(totalPages == 1){
            jQuery('.dataTables_paginate').hide();
          }
          else {
            jQuery('.dataTables_paginate').show();
          }
        },
      });
    }
  };
})(jQuery, Drupal);

