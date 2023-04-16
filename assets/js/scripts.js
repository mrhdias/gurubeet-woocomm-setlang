
/* <![CDATA[ */
/*
 * Last Modification: 2023-04-16 21:05:59
 */


class PopupSetLanguage {
    modalElement = null;
    geoIpData = {};
    localStorageKey = '';
    debug = false;

    constructor(destination, debug = false) {
        // console.log('PopupSetLanguage');

        this.modalElement = document.getElementById(destination);
        if (typeof (this.modalElement) === 'undefined' || this.modalElement === null) {
            throw new Error("Abort Script: no modal destination id");
        }

        this.debug = debug;

        const utf8ToB64 = btoa(encodeURIComponent(window.location.hostname));
        this.localStorageKey = 'customer-set-lang-storage'.concat('-', utf8ToB64.replace('==', ''));
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
        // console.log('Now Date: ' + nowDate);
        const data = {
            'change': change,
            'date': nowDate,
            'max_hours': this.geoIpData['max_hours'],
            'ip': this.geoIpData['ip'],
            'country_codes': {
                'ip': this.geoIpData['country_codes']['ip'],
                'lang': this.geoIpData['country_codes']['lang'],
                'page': this.geoIpData['country_codes']['page']
            }
        }
        this.updateLocalStorage(data);
    }

    removeModal() {
        // console.log('close modal...');
        this.storeCustomerDate(false);
        this.modalElement.remove();
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
            // console.log('close popup...');
            // console.log('ID Modal: ' + event.currentTarget.parentNode.parentNode.parentNode.id);
            // _this.storeCustomerDate(false);
            // test if have a class or id before close
            // event.currentTarget.parentNode.parentNode.parentNode.remove();
            _this.removeModal();
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
            // console.log('clicked in popup button...');

            const langLink = document.head.querySelector('link[hreflang="' + _this.geoIpData['country_codes']['page'] + '"]');
            // console.log('Lang Link Href: ' + langLink.href);
            // const langLink = document.body.querySelector('li > a[hreflang="' + _this.geoIpData['country_codes']['page'] + '"]');
            if (typeof (langLink) != 'undefined' && langLink != null) {
                _this.storeCustomerDate(true);
                // langLink.click();
                window.location.href = langLink.href;
            }
            // console.log('ID Modal: ' + event.currentTarget.parentNode.parentNode.parentNode.id);
            // event.currentTarget.parentNode.parentNode.parentNode.remove();
            _this.modalElement.remove();
        }

        body.appendChild(button);

        return body;
    }

    addFooterContent(footerText) {
        const footer = document.createElement("div");
        footer.classList.add("popup-footer");

        const text = document.createTextNode(footerText);
        footer.appendChild(text);

        let _this = this;
        footer.onclick = function (event) {
            // console.log('clicked in footer');
            _this.removeModal();
        }

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
                // console.log('click modal...');
                _this.removeModal();
                // _this.storeCustomerDate(false);
                // event.currentTarget.remove();
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

        document.body.style.cursor = 'wait';

        const promise = new Promise(function (resolve, reject) {

            const xhr = new XMLHttpRequest();
            const method = "GET";

            xhr.open(method, url, true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    resolve(JSON.parse(xhr.responseText));
                }
                document.body.style.cursor = 'default';
            };
            xhr.send();
        });

        return promise;
    }

    checkIfIsToShow(customerSetLang) {

        if (('date' in customerSetLang) && (customerSetLang['date'] !== '') &&
            ('max_hours' in customerSetLang) && (customerSetLang['max_hours'] > 0)) {

            // console.log('Customer Stored Max Hours: ' + customerSetLang['max_hours']);

            const date = new Date(customerSetLang['date']);
            const nowDate = new Date();
            // console.log('Customer Stored Date: ' + date);
            const difference = nowDate.getTime() - date.getTime();

            const hours = Math.ceil(difference / (1000 * 3600));
            if (this.debug) {
                console.log('Number of hours that passed since last chosen: ' + hours);
            }

            return (hours > customerSetLang['max_hours']);
        }

        return true;
    }

    setCustomerLang(customerSetLang) {
        // console.log('set customer language...');

        if (typeof (document.documentElement.lang) === 'undefined' || document.documentElement.lang === null) {
            return;
        }

        if (customerSetLang['change'] &&
            document.documentElement.lang.toLowerCase() !== customerSetLang['country_codes']['lang']) {
            // console.log('Lang: ' + document.documentElement.lang + ' Page: ' + customerSetLang['country_codes']['page']);
            const langLink = document.body.querySelector('li > a[hreflang="' + customerSetLang['country_codes']['page'] + '"]');
            if (typeof (langLink) != 'undefined' && langLink != null) {
                langLink.click();
            }
        }

        return;
    }

    updateLocalStorage(data) {
        if (localStorage.getItem(this.localStorageKey) !== null) {
            localStorage.removeItem(this.localStorageKey);
        }
        localStorage.setItem(this.localStorageKey, JSON.stringify(data));
    }

    manageStatus() {
        // console.log('manage status');

        if (localStorage.getItem(this.localStorageKey) === null) {
            // when reload the page the local storage item already has been set
            return;
        }

        let _this = this;
        document.body.querySelectorAll('ul.sub-menu > li.menu-item > a:has(> img)').forEach(function (linkLang, index) {
            // console.log('Link Lang: ' + linkLang.href);

            linkLang.onclick = function (event) {
                // event.preventDefault();
                // console.log('click link: ' + event.currentTarget.href);

                const customerSetLang = JSON.parse(localStorage.getItem(_this.localStorageKey));
                if (('change' in customerSetLang) && customerSetLang['change']) {
                    customerSetLang['change'] = false;
                    _this.updateLocalStorage(customerSetLang);
                }
            }
        });
    }

    geoIP() {

        const lang_country_code = (typeof (document.documentElement.lang) === 'undefined' || document.documentElement.lang === null) ? '' : document.documentElement.lang;
        // console.log('Lang: ' + lang_country_code);
        // console.log('Location: ' + document.location);

        let url = new URL('wp-content/plugins/gurubeet-woocomm-setlang/geoip.php', document.location.origin);
        url.searchParams.append('version', '2023041601');
        url.searchParams.append('lang_country_code', lang_country_code.toLowerCase());
        // console.log('URL: ' + url.href);

        let _this = this;
        this.getDataFromGeoIP(url).then(function (results) {
            if (_this.debug) {
                console.log('Result: ' + JSON.stringify(results));
            }
            if ("error" in results) {
                console.log('Error: ' + results["error"]);
            } else if (("skip" in results) && results["skip"]) {
                console.log('Skip popup...');
            } else {
                // console.log('if is to show the popup...');
                if (_this.geoIpData['ip'] != results['ip']) {
                    if (localStorage.getItem(_this.localStorageKey) !== null) {
                        localStorage.removeItem(_this.localStorageKey);
                    }
                    _this.geoIpData = results;
                    _this.buildPopup(results);
                    // comment for debug
                    _this.modalElement.style.display = "block";
                } else {
                    _this.storeCustomerDate(false);
                }
            }
        });
    }

    init() {
        // console.log('init');
        // console.log('Test Local Storage: ' + localStorage.getItem('test-test'));

        this.geoIpData['ip'] = '';
        if (localStorage.getItem(this.localStorageKey) !== null) {
            const customerSetLang = JSON.parse(localStorage.getItem(this.localStorageKey));

            if (!this.checkIfIsToShow(customerSetLang)) {
                this.setCustomerLang(customerSetLang);
                return;
            }
            if (('ip' in customerSetLang) && (customerSetLang['ip'] !== '')) {
                this.geoIpData['max_hours'] = this.geoIpData['max_hours'];
                this.geoIpData['ip'] = customerSetLang['ip'];
                this.geoIpData['country_codes'] = {
                    'ip': customerSetLang['country_codes']['ip'],
                    'lang': customerSetLang['country_codes']['lang'],
                    'page': customerSetLang['country_codes']['page']
                }
            }
        }

        const sleep = ms => new Promise(resolve => setTimeout(resolve, ms));

        let _this = this;
        async function sleepyPopupSetLanguage() {
            // console.log("I'm going to sleep for 5 second.");
            await sleep(5000);
            // console.log("I woke up after 5 second.");

            _this.geoIP();

            _this.manageStatus();
        }

        sleepyPopupSetLanguage();

    }

}

document.addEventListener("DOMContentLoaded", function (event) {
    // console.log('Hostname: ' + window.location.hostname + ' Pathname: ' + window.location.pathname);
    console.log('Init PopupSetLanguage');

    let popSetLanguage = new PopupSetLanguage('modal-set-language');
    popSetLanguage.init();
});


/* ]]> */
