/**
 * 多个 INPUT 联动
 */
class LinkageInput {

    constructor(eleInputs, data) {
        LinkageInput.initLinkage(eleInputs, data)
    }


    /**
     * setDataList
     * @param eleInput
     * @param values
     */
    static setDataList(eleInput, values) {

        let dataListId = 'list-' + eleInput.id

        let eleDataList = document.getElementById(dataListId)

        if (!eleDataList) {
            eleDataList = document.createElement('datalist')
            eleInput.after(eleDataList) // 在DOM元素后面插入新元素
            eleDataList.id = dataListId
        }

        while (eleDataList.firstChild) {
            eleDataList.removeChild(eleDataList.firstChild)
        }

        for (let value of values) {
            let option = document.createElement('option')
            option.value = value
            eleDataList.appendChild(option)
        }

        eleInput.setAttribute('list', dataListId)
    }

    static initLinkage(eleInputs, data) {

        function getListByLayer(layer) {

            let obj = data

            for (let i = 0; i < layer; i++) {
                obj = obj[eleInputs[i].value]
                if (!obj) return []
            }

            return (obj instanceof Array) ? obj : Object.keys(obj)
        }


        for (let i = 0; i < eleInputs.length - 1; i++) {

            this.bindDelayEvents(eleInputs[i], 'change', () => {
                let nextInput = eleInputs[i + 1]
                let values = getListByLayer(i + 1)
                if (values.indexOf(nextInput.value) < 0) nextInput.value = ''
                this.setDataList(nextInput, values)
                // 通过代码修改 input.value，不会触发 change 事件？
                // 那就手动触发
                nextInput.dispatchEvent(new Event('change'))
            })
        }

        for (let i = 0; i < eleInputs.length; i++) {
            let eleInput = eleInputs[i]
            eleInput.addEventListener('focus', () => {
                let dataListId = 'list-' + eleInput.id
                let values = getListByLayer(i)
                if (!document.getElementById(dataListId)) this.setDataList(eleInput, values)
            })
        }

        this.setDataList(eleInputs[0], getListByLayer(0))

    }

    static bindDelayEvents(eles, eTypes, callback, ...moreargs) {

        let stid = 0

        function func(...args) {
            clearTimeout(stid)
            stid = setTimeout(callback.bind(this, ...args), 16)
        }

        // 既可以是数组，也可以是空格分隔的字符串
        (eTypes instanceof Array) || (eTypes = eTypes.split(/\s+/));
        (eles instanceof Element) && (eles = [eles]);
        (eles instanceof Array) || (eles = eles.split(','));

        for (let eType of eTypes) {
            for (let ele of eles) {
                (ele instanceof Element) || (ele = document.querySelector(ele));
                ele && ele.addEventListener(eType, func, ...moreargs);
            }
        }
    }
}