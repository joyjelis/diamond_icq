var config = {
    paths: {
        'magneto/lazyload': 'Magneto_GlobalTel/js/plugins/lazyload.min',
        'magneto/floating-label': 'Magneto_GlobalTel/js/plugins/floating-label.min',
    },

    shim: {
        'magneto/lazyload': {
            deps: ['jquery']
        },
        'magneto/floating-label': {
            deps: ['jquery']
        },
    },
    'config': {
        'mixins': {
            'mage/validation': {
                'Magneto_GlobalTel/js/gtel-mixin': true
            }
        }
    }
};