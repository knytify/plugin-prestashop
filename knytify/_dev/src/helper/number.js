
const numberDisplayFormatter = function (value) {
    if (value > 1000) {
        return value / 1000 + "K";
    } else {
        return value;
    }
}

export {
    numberDisplayFormatter
}