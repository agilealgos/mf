import React, {Component} from 'react';
import { connect } from "react-redux";
import SingleField from './SingleField';
import { isPlainObject } from 'lodash';

class CardFields extends Component
{
    static mapStateToProps(state, props) {
        return props;
    };

    render() 
    {
        return (
            <div className="flex flex-col space-y-3">
                    {
                        this.props.card.getFields().filter(field => !!field).map(field => {
                            return isPlainObject(field)? new SingleField(field) : field;
                        })
                        .map(field => {
                            return field.render();
                        })
                    }
                </div>
        );
    }
}

export default connect(CardFields.mapStateToProps, {})(CardFields);