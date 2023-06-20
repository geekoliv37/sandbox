/*const calcButton = document.querySelector('.btn-primary');
const dateFacture = document.getElementById('inputdate');
const conditionReglement = document.getElementById('exampleSelect1');
const calculRequest = new XMLHttpRequest();
console.log(calcButton.value);
calcButton.addEventListener('click',function(event){
    event.preventDefault();
    requestCalcul();
});
function requestCalcul(){
    const dateValue = dateFacture.value;
    const conditionValue = conditionReglement.value;
    document.replace("http://127.0.0.1:8000/?date=");

}*/

    const button = document.querySelector('.btn');
    button.addEventListener('click', function() {
        const inputDate = document.getElementById('inputdate').value;
        const selectedOption = document.getElementById('exampleSelect1').value;
        const encodedInputDate = encodeURIComponent(inputDate);
        const encodedSelectedOption = encodeURIComponent(selectedOption);
        window.location.href = '/?date=' + encodedInputDate + '&option=' + encodedSelectedOption;
    });

