function teste(){
    const myModal = document.getElementById('exampleModal');
    myModal.addEventListener('shown.bs.modal', () => {
        myInput.focus();
    })
}