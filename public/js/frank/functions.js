function queryStringToObject(queryString = location.search.substr(1)) {

    let obj = {}
    let strs = queryString.split('&')

    for (let str of strs) {

        let pair = str.split('=')

        if (!pair[0]) continue

        let objRef = obj

        let paths = decodeURIComponent(pair[0]).split(/[\[\]]+/)

        let key = paths[0]

        if (paths.length > 1) {

            paths.pop()

            key = paths.pop()

            for (let path of paths) {
                if (!objRef[path]) {
                    objRef[path] = {}
                }
                objRef = objRef[path]
            }

        }

        objRef[key] = pair[1] ? decodeURIComponent(pair[1]) : ''

    }

    return obj
}

function objectToQueryString(obj) {

    let strs = []

    function cook(obj, prefix = '') {

        for (let key in obj) {

            let val = obj[key]
            key = prefix ? `${prefix}[${key}]` : key

            if (val instanceof Object) {
                cook(val, key)
            } else {
                key = encodeURIComponent(key)
                val = encodeURIComponent(val)
                strs.push(`${key}=${val}`)
            }
        }

    }

    cook(obj)

    return strs.join('&')
}

function objectFilte(obj, keys = [], except = true) {

    let map = {}

    for (let key of keys) {
        map[key] = true
    }

    let result = {}

    for (let k in obj) {
        if (except) {
            if (map[k]) continue
        } else {
            if (!map[k]) continue
        }
        result[k] = obj[k]
    }

    return result
}

/**
 * 选中文本
 */
function selectText(ele) {
    if (document.selection) {
        var range = document.body.createTextRange();
        range.moveToElementText(ele);
        range.select();
    } else if (window.getSelection) {
        window.getSelection().empty();
        var range = document.createRange();
        range.selectNodeContents(ele);
        window.getSelection().addRange(range);
    }
}