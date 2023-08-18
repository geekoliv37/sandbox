const calc = document.querySelector('.btn-primary');
const valueHt = document.getElementById('priceHT');
const caseAutoliquidation = document.getElementById('Select1');

function editcompta(){
    document.location.replace("http://127.0.0.1:8000/date/?montant="+valueHt.value+"&case="+caseAutoliquidation.textContent)
}
calc.addEventListener('click',function(event){
    event.preventDefault();
    editcompta();
});

