const hasNumber = (str) => {
    return /\d/.test(str);
}

const hasLetter = (str) => {
    return /[a-zA-Zа-яА-Я]/u.test(str);
}

const fields = [
    { id: "client_name", errorClass: "client_name_error", validate: (v) => v === "" || hasNumber(v), msg: "Укажите корректное ФИО клиента" },
    { id: "courier_name", errorClass: "courier_name_error", validate: (v) => v === "" || hasNumber(v), msg: "Укажите корректное ФИО курьера" },
    { id: "client_phone", errorClass: "client_phone_error", validate: (v) => v === "" || v.length < 17, msg: "Укажите корректный номер телефона" },
    { id: "client_mail", errorClass: "email_error", validate: (v) => v === "" || !white_list_email_domains.includes(v.split("@")[1]), msg: "Укажите корректную эл. почту" },
    { id: "product", errorClass: "product_error", validate: (v) => v === "", msg: "Укажите корректное название товара"},
    { id: "price", errorClass: "price_error", validate: (v) => v === "" || v < 1, msg: "Укажите корректную стоимость товара" },
    { id: "article", errorClass: "article_error", validate: (v) => !/^[A-ZА-Я]{3}-\d{4}$/.test(v), msg: "Формат: 0000" },
    { id: "phone", errorClass: "phone_error", validate: (v) => v.length < 17, msg: "Формат: +7 ___-__-__" },
    { id: "street", errorClass: "street_error", validate: (v) => v === "", msg: "Укажите корректную улицу" },
    { id: "house", errorClass: "house_error", validate: (v) => v === "" || hasLetter(v[0]) || hasSpecial(v[0]) || v < 1, msg: "Укажите корректный номер дома" },
    { id: "entrance", errorClass: "entrance_error", validate: (v) => (v.length > 0 && v < 1) || hasLetter(v), msg: "Укажите корректный номер подъезда" },
    { id: "apartment", errorClass: "apartment_error", validate: (v) => v === "" || hasLetter(v) || hasSpecial(v[0]) || v < 1, msg: "Укажите корректный номер квартиры" },
    { id: "floor", errorClass: "floor_error", validate: (v) => (v.length > 0 && v < 1) || hasLetter(v), msg: "Укажите корректный номер этажа" },
    { id: "delivery_date", errorClass: "delivery_date_error", validate: (v) => v === "", msg: "Укажите корректную дату доставки"},
];

const validateField = (input, errorClass, validationFunc, msg) => {
    const errorElement = document.querySelector(`.${errorClass}`);
    
    if (validationFunc(input.value)) {
        errorElement.innerText = msg;
    } else {
        errorElement.innerText = "";
    }
}

fields.forEach(({ id, errorClass, validate, msg }) => {
    const input = document.getElementById(id);
    input.addEventListener("blur", () => validateField(input, errorClass, validate, msg));
    input.addEventListener("focus", () => document.querySelector(`.${errorClass}`).innerText = "");
});
