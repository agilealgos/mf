import Row from '../Row';
import Column from '../Column';
import Context from '../Context';
import ConditionOrFilter from '../conditionsOrFilters/ConditionOrFilter';
import Offer from '../conditionsOrFilters/Offer';

export default class RowsMapper
{
    constructor(rows) 
    {
        this.rows = rows || [];
    }

    map() 
    {
        for (let [rowIndex, row] of this.rows.entries()) {
            row = Row.map(row);
            this.rows[rowIndex] = row;

            for (let [columnIndex, column] of row.columns.entries()) {
                column = Column.map(column);
                row.columns[columnIndex] = column;

                for (let [offerIndex, offer] of column.defaultOffers.entries()) {
                    offer = Offer.map(offer);
                    column.defaultOffers[offerIndex] = offer;
                }  

                for (let [contextIndex, context] of column.contexts.entries()) {
                    context = Context.map(context);
                    column.contexts[contextIndex] = context;

                    for (let [conditionOrFilterIndex, conditionOrFilter] of context.conditionsOrFilters.entries()) {
                        conditionOrFilter = ConditionOrFilter.map(conditionOrFilter);
                        context.conditionsOrFilters[conditionOrFilterIndex] = conditionOrFilter;
                        
                        //conditions or filters
                    }  
                }   
            }
        }

        return this.rows;
    }
}