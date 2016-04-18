
function doAction(logid, status){
   action=$( "#action_" + logid + " option:selected" ).val();
   comment=$( "#comment_" + logid ).val();
   link=$( "#link_" + logid ).val();
   switch (action) {
      case "validate": update(logid, 1, comment, link);break;
      case "unvalidate": update(logid, 2, comment, link);break;
      case "update": update(logid, status, comment, link);break;
      case "new": update(logid, 0, comment, link);break;
      case "reinit": reinit(logid);break;
      case "link": update_link(logid);break;
   }
}

function update(logid, status, comment, link){
   $( "#loader" ).show();
   $.post("update_log.php", {"logid" : logid, "status" : status, "comment" : comment, "link" : link }).success(function( data ) {
        if(data.status == 'success'){
           action=$( "#action_" + logid + " option:selected" ).val();
           comment=$( "#comment_" + logid ).val();
           link=$( "#link_" + logid ).val(); 
           window.location.reload(false);
        } else if(data.status == 'error'){
            alert(data.msg);
        }
    });
}

function reinit(logid){
   if (confirm("Êtes-vous sûr de vouloir réinitialiser le message ?")) {    
   $( "#loader" ).show();
   $.post("reinit_log.php", {"logid" : logid}).success(function( data ) {
        if(data.status == 'success'){
           action=$( "#action_" + logid + " option:selected" ).val();
           comment=$( "#comment_" + logid ).val();
           link=$( "#link_" + logid ).val(); 
           window.location.reload(false);
        } else if(data.status == 'error'){
            alert(data.msg);
        }
    });
   }
}

function update_link(logid){
   link=prompt("Entrer un lien :","");
   if (link != null){
   $( "#loader" ).show();
   $.post("update_link.php", {"logid" : logid, "link" : link}).success(function( data ) {
        if(data.status == 'success'){
           action=$( "#action_" + logid + " option:selected" ).val();
           comment=$( "#comment_" + logid ).val();
           link=$( "#link_" + logid ).val(); 
           window.location.reload(false);
        } else if(data.status == 'error'){
            alert(data.msg);
        }
    });
   }
}

function resize(logid){
   autosize($( "#comment_" + logid ));
}

$(document).ready(function() {
    $('.action').prop('selectedIndex',0);

});

