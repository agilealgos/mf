import React, {Component} from 'react';
import { connect } from "react-redux";
import _ from 'lodash';
import ConditionOrFilter from './conditionsOrFilters/ConditionOrFilter';
import CardCreator from './conditionsOrFilters/CardCreator';
import ContextActions from './actions/ContextActions';
import CardHeader from './CardHeader';
import CardFields from './CardFields';

const {removeConditionOrFilter} = ContextActions;

class Testable extends Component
{
    static mapStateToProps(state, props) {
        return _.merge({conditionOrFilter: {}}, {conditionOrFilter: ConditionOrFilter.structure}, props);
    };

    render() 
    {
        const conditionOrFilter = CardCreator(this.props.testableType, this.props.conditionOrFilter);

        return (
            <div className="
                relative rounded-3 bg-gray-100 w-full p-3 space-y-4 shadow-card
                before:block before:w-[92%] before:h-full before:absolute before:rounded-3 before:z--1 before:top-2 before:bg-[#eceff1] before:z-negative-1
            ">
                <CardHeader card={conditionOrFilter} onClose={() => { 
                    this.props.removeConditionOrFilter(
                        conditionOrFilter.data.temporaryID,
                        this.props.contextTemporaryID,
                        this.props.columnTemporaryID
                    )}} />
                <CardFields card={conditionOrFilter}/>                 
            </div>
        );
    }
}

export default connect(Testable.mapStateToProps, {removeConditionOrFilter})(Testable);