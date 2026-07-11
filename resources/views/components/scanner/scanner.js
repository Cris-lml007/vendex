let html5QrcodeScanner = null;


document.getElementById('btn-camera').addEventListener('click',()=>{

    if(html5QrcodeScanner == null){
        html5QrcodeScanner = new window.Html5QrcodeScanner('scanner',
            {
                fps: 30,
                qrbox: {
                    width: 250,
                    height: 250
                }
            });
    }

    html5QrcodeScanner.render((decodedText, decodedResult) => {
        console.log(decodedText)
        $('#modal-scanner').modal('hide');
        $wire.product_id = decodedText;
        html5QrcodeScanner.clear()
    }, (error) =>{
    })
});
