

const formatHttpError = (err, err_msg, translator) => {
    const err_key = err.response?.data;
    if (err_key) {
        err_msg += ": " + translator(err_key);
    }
    return err_msg
}

export {
    formatHttpError
}