import mock from './mock'
// dashboard
import './data/dashboard/ecommerce'

// pages
import './data/pages/account-setting'
/* eslint-enable import/extensions */

mock.onAny().passThrough() // forwards the matched request over network
