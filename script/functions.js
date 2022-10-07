function deny(id){
    $.get( "approve-boulder-page-API.php",  {"method":"deny_problem","fantaboulder_id":id},function(data){
        $("#card_"+id).remove();
    });
}

function approve(id){

 $.get( "approve-boulder-page-API.php",  {"method":"approve_problem","fantaboulder_id":id},function(data){
        $("#card_"+id).remove();
        console.log(data);
    });
   
}