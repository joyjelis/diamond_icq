var config = {
    map: {
        '*': {
            'addresspopup': "Customer_Sell/js/addresspopup"
        }
    },
    paths: {
        'sell': "Customer_Sell/js/sell",
        'imagePreview': "Customer_Sell/js/Image-preview",
        'getQuote': "Customer_Sell/js/get-quote",
    },
    shim: {
        'sell': {
            deps: ['jquery']
        },
        'imagePreview': {
            deps: ['jquery']
        },
        'getQuote': {
            deps: ['jquery']
        }
    }
}