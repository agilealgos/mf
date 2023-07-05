import shortid from 'shortid';
import _ from 'lodash';

export default (object, empty) => (_.merge(object, {"temporaryID": empty? '' : shortid.generate()}));