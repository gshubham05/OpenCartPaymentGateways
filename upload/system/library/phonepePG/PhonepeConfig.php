<?php

class PhonepeConstantsPG{
    CONST PPBASE_URL_PROD                       = "https://api.phonepe.com/apis/hermes";

    CONST TRANSACTION_URL_PRODUCTION            = "https://api.phonepe.com/apis/hermes/pg/v1/pay";

    CONST TRANSACTION_STATUS_URL_PRODUCTION     = "https://api.phonepe.com/apis/hermes/pg/v1/status/";

    CONST PPBASE_URL_STAGE                      = "https://api-preprod.phonepe.com/apis/hermes";
    CONST TRANSACTION_URL_STAGING               = "https://api-preprod.phonepe.com/apis/hermes/pg/v1/pay";
    CONST TRANSACTION_STATUS_URL_STAGING        = "https://api-preprod.phonepe.com/apis/hermes/pg/v1/status/";

    // CONST PPBASE_URL_STAGE                      = "https://api-testing.phonepe.com/apis/hermes";
    // CONST TRANSACTION_URL_STAGING               = "https://api-testing.phonepe.com/apis/hermes/pg/v1/pay";
    // CONST TRANSACTION_STATUS_URL_STAGING        = "https://api-testing.phonepe.com/apis/hermes/pg/v1/status/";

    CONST Test_Script                           = "https://mercury-stg.phonepe.com/linchpin/checkout/0.4.13/phonepe.js";
    CONST Prod_Script                           = "https://mercury.phonepe.com/linchpin/checkout/1.0.4/phonepe.js";

    CONST EVENTS_PUSH_URL_STAGING               = "https://api-preprod.phonepe.com/apis/hermes/plugin/ingest-event";  //staging
    CONST EVENTS_PUSH_URL_PROD                  = "https://api.phonepe.com/apis/hermes/plugin/ingest-event";  //staging

    CONST Test_Env                              = "Preprod Environment";
    CONST Prod_Env                              = "Prod Environment";

    CONST SAVE_PHONEPE_RESPONSE                 = true;
    CONST APPEND_TIMESTAMP                      = true;
    CONST SUCCESS                               = "PAYMENT_SUCCESS";
    CONST PENDING                               = "PAYMENT_PENDING";
    CONST ERROR                                 = "PAYMENT_ERROR";
    CONST SERVER_ERROR                          = "INTERNAL_SERVER_ERROR";
    CONST TXN_NOT_FOUND                         = "TRANSACTION_NOT_FOUND";
    CONST ONLY_SUPPORTED_INR                    = true;

    CONST MAX_RETRY_COUNT                       = 10;
    CONST CONNECT_TIMEOUT                       = 10;
    CONST TIMEOUT                               = 10;

    CONST LAST_UPDATED                          = "20200120";
    CONST PLUGIN_VERSION                        = "1.0";
}

?>