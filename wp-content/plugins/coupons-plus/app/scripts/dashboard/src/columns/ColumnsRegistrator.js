import SimpleColumn from './SimpleColumn';
import ANDColumn from './ANDColumn';
import ORColumn from './ORColumn';
import SimpleOffersColumn from './offerColumns/SimpleOffersColumn';
import ANDOffersColumn from './offerColumns/ANDOffersColumn';
import OROffersColumn from './offerColumns/OROffersColumn';
import MultiOffersColumn from './offerColumns/MultiOffersColumn';
import TieredOffersColumn from './offerColumns/TieredOffersColumn';

export default class ColumnsRegistrator
{
    static registeredColumns = [
        SimpleColumn,
        ANDColumn,
        ORColumn,

        SimpleOffersColumn,
        ANDOffersColumn,
        OROffersColumn,
        MultiOffersColumn,
        TieredOffersColumn,
    ];

    static getAll() : array
    {
        return ColumnsRegistrator.registeredColumns;
    }

    static getByType(type)
    {
        return ColumnsRegistrator.registeredColumns.find(Column => {
            return Column.TYPE === type
        });
    }
}