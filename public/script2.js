    const calcButton = document.querySelector('.btn-primary');
    const dateFacture = document.getElementById('inputdate');
    const conditionReglement = document.getElementById('exampleSelect1');

    calcButton.addEventListener('click',function(event){
    event.preventDefault();
    requestCalcul();
});

    function requestCalcul(){
    document.location.replace("http://127.0.0.1:8000/date/?date="+dateFacture.value+"&condition="+conditionReglement.value) ;
}
