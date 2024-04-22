import app from '@/main'

export function toCamel(s) {
    return s.replace(/([-_][a-z])/ig, ($1) => {
        return $1.toUpperCase()
            .replace('-', '')
            .replace('_', '');
    });
}

export function isArray(a) {
    return Array.isArray(a);
}

export function isObject(o) {
    return o === Object(o) && !isArray(o) && typeof o !== 'function';
}

export function keysToCamel(o) {
    if (o == null) return null;
    if (isObject(o)) {
        const n = {};

        Object.keys(o)
            .forEach((k) => {
                n[toCamel(k)] = keysToCamel(o[k]);
            });

        return n;
    } else if (isArray(o)) {
        return o.map((i) => {
            return keysToCamel(i);
        });
    }
    return o;
}

export function textToCamel(s) {
    return s.replace(/([-_][a-z])/ig, ($1) => {
        return $1.toUpperCase()
            .replace('-', '')
            .replace('_', '');
    });
}


export function alert() {
    app.$vs.notify(...arguments);
}

export function sweet() {
    app.$swal(...arguments);
}

export function defaultHandleError(err) {
    // if (process.env.NODE_ENV !== 'production')

    if (err.response && err.response.data) {
        err = err.response;
    }
    let params = {
        title: `Thông báo`,
        text: err.data.message,
        icon: 'warning',
        confirmButtonText: 'Xác nhận',
        customClass: {
            confirmButton: 'btn btn-danger right',
        },
        buttonsStyling: false,
    };
    sweet(params);
}

export function handleErrorWithMessage(message) {

    let params = {
        title: `Thông báo`,
        text: message,
        icon: 'warning',
        confirmButtonText: 'Xác nhận',
        customClass: {
            confirmButton: 'btn btn-danger right',
        },
        buttonsStyling: false,
    };

    sweet(params);
}

export function defaultHandleSuccess(res) {
    // if (process.env.NODE_ENV !== 'production') console.log(res);

    if (res.data.message == "") return;

    let params = {
        timer: res.config.upTime || 1000,
        title: 'Good job!',
        text: res.data.message,
        icon: 'success',
        customClass: {
            confirmButton: 'btn btn-primary',
        },
        buttonsStyling: false,
    };
    sweet(params);
}

export function showMessage(message, color) {
    let params = {
        time: 4000,
        active: true,
        title: `Thông báo`,
        text: message,
        color: color,
        iconPack: 'feather',
        position: 'top-center',
        icon: 'icon-message-square'
    };
    alert(params)
}

export default {
    numberFormat(number) {
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    },
    getUrlParam(key) {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(key);
    },
}

export function compareValues(key, order = 'asc') {
    return function (a, b) {
        if (!a.hasOwnProperty(key) || !b.hasOwnProperty(key)) {
            return 0
        }

        const varA = (typeof a[key] === 'string') ?
            a[key].toUpperCase() : a[key]
        const varB = (typeof b[key] === 'string') ?
            b[key].toUpperCase() : b[key]

        let comparison = 0
        if (varA > varB) {
            comparison = 1
        } else if (varA < varB) {
            comparison = -1
        }
        return (
            (order == 'desc') ? (comparison * -1) : comparison
        )
    }
}

export function toastNotify(component, title, text, icon = 'CoffeeIcon', variant = 'success', position = 'top-right') {
    app.$toast({
        component: component,
        position: position,
        props: {
            title: title,
            icon: icon,
            variant: variant,
            text: text,
        },
    })
}
export function callDialogConfirm (callback, color = 'danger', message = 'Bạn có chắc chắn thực hiện hành động này ?', title = 'Thông báo') {
    app.$swal({
        title: title,
        text: message,
        confirmButtonText: 'Xác nhận',
        cancelButtonText: 'Hủy',
        customClass: {
            cancelButton: `btn btn-outline-warning`,
            confirmButton: `btn btn-${color} mr-1`,
        },
        showCancelButton: true,
        buttonsStyling: false,
    }).then(result => {
        if (result.value) {
            callback()
        }
    })
    // this.$vs.dialog({
    //   type: 'confirm',
    //   color: color,
    //   title: 'Thông báo',
    //   text: 'Bạn có chắc chắn thực hiện hành động này ?',
    //   acceptText: 'Xác nhận',
    //   cancelText: 'Hủy',
    //   accept: function () {
    //     callback()
    //   }
    // })
}

export function perPageOptionDefault(options = [10, 25, 50, 100]) {
    return options
}


