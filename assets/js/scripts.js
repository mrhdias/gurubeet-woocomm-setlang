/* <![CDATA[ */
/*
 * Last Modification: Mon Dec 19 22:25:50 WET 2022
 */


class PopupSetLanguage {
    modalElement = null;
    geoIpData = {}

    constructor(destination) {
        console.log('PopupSetLanguage');

        this.modalElement = document.getElementById(destination);
        if (typeof (this.modalElement) === 'undefined' || this.modalElement === null) {
            throw new Error("Abort Script: no modal destination id");
        }

    }

    // setNewUrl() {
    //     if (document.documentElement.lang.toLowerCase() === this.geoIpData['country_codes']['default']) {
    //         return document.location.origin + '/' + this.geoIpData['country_codes']['page'] + document.location.pathname;
    //     }
    //
    //     var parts = document.location.pathname.split('/');
    //     if (parts.length >= 2 && parts[0] === '') {
    //         parts[1] = this.geoIpData['country_codes']['page'];
    //         return document.location.origin + '/' + parts.join('/') + document.location.pathname;
    //     }
    //     return '';
    // }

    storeCustomerDate(change) {
        const nowDate = new Date().toJSON();
        console.log('Now Date: ' + nowDate);
        const data = {
            'change': change,
            'date': nowDate,
            'ip': this.geoIpData['ip'],
            'country_codes': {
                'ip': this.geoIpData['country_codes']['ip'],
                'lang': this.geoIpData['country_codes']['lang'],
                'page': this.geoIpData['country_codes']['page']
            }
        }
        localStorage.setItem('customer-set-lang-storage', JSON.stringify(data));
    }

    addHeaderContent(headerText) {
        const header = document.createElement("div");
        header.classList.add("popup-header");

        const title = document.createElement("span");
        title.classList.add("popup-title");
        const text = document.createTextNode(headerText);
        title.appendChild(text);

        header.appendChild(title);

        const iconClose = document.createElement("i");
        iconClose.classList.add("popup-icon");
        header.appendChild(iconClose);

        let _this = this;
        iconClose.onclick = function (event) {
            console.log('close popup...');
            console.log('ID Modal: ' + event.currentTarget.parentNode.parentNode.parentNode.id);
            _this.storeCustomerDate(false);
            // test if have a class or id before close
            event.currentTarget.parentNode.parentNode.parentNode.remove();

        }

        return header;
    }

    addBodyContent(bodyTexts) {
        const body = document.createElement("div");
        body.classList.add("popup-body");

        const information = document.createElement("span");
        information.classList.add("popup-information");
        const textInfo = document.createTextNode(bodyTexts['information']);
        information.appendChild(textInfo);

        body.appendChild(information);

        const button = document.createElement("button");
        button.type = "button";
        button.name = "change-language";
        button.value = this.geoIpData['country_codes']['page'];
        button.classList.add("popup-button");

        const textButtonImg = document.createElement("img");
        textButtonImg.src = bodyTexts['button']['flag'];
        button.appendChild(textButtonImg);

        const textButtonField = document.createElement("span");
        const textButton = document.createTextNode(bodyTexts['button']['text']);
        textButtonField.appendChild(textButton);

        button.appendChild(textButtonField);

        // <link rel="alternate" hreflang="en" href="https://www.example.com/product/" />
        // <link rel="alternate" hreflang="pt-pt" href="https://www.example.com/pt-pt/produto/" />
        // <link rel="alternate" hreflang="es" href="https://www.example.com/es/producto/" />
        // <link rel="alternate" hreflang="x-default" href="https://www.example.com/product/" />

        let _this = this;
        button.onclick = function (event) {
            console.log('clicked in popup button...');

            const langLink = document.head.querySelector('link[hreflang="' + _this.geoIpData['country_codes']['page'] + '"]');
            // console.log('Lang Link Href: ' + langLink.href);
            // const langLink = document.body.querySelector('li > a[hreflang="' + _this.geoIpData['country_codes']['page'] + '"]');
            if (typeof (langLink) != 'undefined' && langLink != null) {
                _this.storeCustomerDate(true);
                // langLink.click();
                window.location.href = langLink.href;
            }
            // console.log('ID Modal: ' + event.currentTarget.parentNode.parentNode.parentNode.id);
            event.currentTarget.parentNode.parentNode.parentNode.remove();
        }

        body.appendChild(button);

        return body;
    }

    addFooterContent(footerText) {
        const footer = document.createElement("div");
        footer.classList.add("popup-footer");

        const text = document.createTextNode(footerText);
        footer.appendChild(text);

        return footer;
    }


    buildPopup(config) {

        const popup = document.createElement("div");
        popup.classList.add("popup");

        const header = this.addHeaderContent(config['texts']['header']);
        popup.appendChild(header);

        const body = this.addBodyContent(config['texts']['body']);
        popup.appendChild(body);

        const footer = this.addFooterContent(config['texts']['footer']);
        popup.appendChild(footer);

        this.modalElement.appendChild(popup);

        let _this = this;
        this.modalElement.onclick = function (event) {
            const isClickInside = popup.contains(event.target)
            if (!isClickInside) {
                console.log('click modal...');
                _this.storeCustomerDate(false);
                event.currentTarget.remove();
            }
        }

        // document.addEventListener('click', event => {
        //     const isClickInside = popup.contains(event.target)
        //     if (!isClickInside) {
        //         console.log('click modal...');
        //     }
        // });
    }

    getDataFromGeoIP(url) {

        const promise = new Promise(function (resolve, reject) {

            const xhr = new XMLHttpRequest();
            const method = "GET";

            xhr.open(method, url, true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    resolve(JSON.parse(xhr.responseText));
                }
            };
            xhr.send();
        });

        return promise;
    }

    checkIfIsToShow(results) {
        if (!localStorage.getItem('customer-set-lang-storage')) {
            return true;
        }

        const customerSetLan = JSON.parse(localStorage.getItem('customer-set-lang-storage'));
        if (('ip' in customerSetLan) && (customerSetLan['ip'] !== results['ip'])) {
            return true;
        }

        if (('date' in customerSetLan) && (customerSetLan['date'] !== '')) {
            const date = new Date(customerSetLan['date']);
            const nowDate = new Date();
            console.log('Customer Stored Date: ' + date);
            const difference = nowDate.getTime() - date.getTime();

            const hours = Math.ceil(difference / (1000 * 3600));
            console.log('Number of hours that passed since last chosen: ' + hours);

            return (hours > parseInt(results['max_hours']));
        }

        return true;
    }

    setCustomerLang() {
        console.log('set customer language...');

        if (typeof (document.documentElement.lang) === 'undefined' || document.documentElement.lang === null) {
            return;
        }

        if (localStorage.getItem('customer-set-lang-storage') === null) {
            return;
        }

        const customerSetLan = JSON.parse(localStorage.getItem('customer-set-lang-storage'));

        if (customerSetLan['change'] && document.documentElement.lang.toLowerCase() !== customerSetLan['country_codes']['lang']) {
            // console.log('Lang: ' + document.documentElement.lang + ' Page: ' + customerSetLan['country_codes']['page']);
            const langLink = document.body.querySelector('li > a[hreflang="' + customerSetLan['country_codes']['page'] + '"]');
            if (typeof (langLink) != 'undefined' && langLink != null) {
                langLink.click();
            }
        }

        return;
    }

    init() {
        console.log('init');

        const lang_country_code = (typeof (document.documentElement.lang) === 'undefined' || document.documentElement.lang === null) ? '' : document.documentElement.lang;
        console.log('Lang: ' + lang_country_code);
        console.log('Location: ' + document.location);

        let url = new URL('wp-content/plugins/gurubeet-woocomm-setlang/geoip.php', document.location.origin);
        url.searchParams.append('version', '2022121801');
        url.searchParams.append('lang_country_code', lang_country_code.toLowerCase());
        console.log('URL: ' + url.href);

        let _this = this;
        this.getDataFromGeoIP(url).then(function (results) {
            console.log('Result: ' + JSON.stringify(results));
            if ("error" in results) {
                console.log('Error: ' + results["error"]);
            } else if (("skip" in results) && results["skip"]) {
                console.log('Skip popup...');
            } else {
                console.log('Check if is to show the popup...');
                if (_this.checkIfIsToShow(results)) {
                    if (localStorage.getItem('customer-set-lang-storage')) {
                        localStorage.removeItem('customer-set-lang-storage');
                    }
                    _this.geoIpData = results;
                    _this.buildPopup(results);
                    // comment for debug
                    _this.modalElement.style.display = "block";
                } else {
                    _this.setCustomerLang();
                }
            }
        });
    }
}


function main() {
    console.log('URL: ' + window.location.pathname);

    let popSetLanguage = new PopupSetLanguage('modal-set-language');
    popSetLanguage.init();

}

window.onload = function () {
    main();
};


/* ]]> */
