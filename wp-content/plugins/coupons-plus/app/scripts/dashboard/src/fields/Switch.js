import React, {Component, Fragment} from 'react';
import { connect } from "react-redux";
import { Listbox, Transition, Switch } from '@headlessui/react'
import FieldsActions from '../actions/FieldsActions';
import _ from 'lodash';

import DatePicker from "react-datepicker";

const {newSelectValue} = FieldsActions

class Select extends Component
{
    static mapStateToProps(state, props) {
        return props;
    };

    render() 
    {
        return (
            <div className="flex flex-col space-y-2">
                {this.props?.labels?.top ? (
                    <span>
                        {this.props.labels.top}
                    </span>
                ) : ''}
                <div className="flex flex-row items-center space-x-2">
                    <Switch
                        checked={this.props.getValue()}
                        onChange={() => this.props.newSelectValue(
                           !this.props.getValue(), this.props.temporaryID, this.props.propertyName
                       )}
                        className={`${this.props.getValue() ? 'bg-blue-normal' : 'bg-gray-400'}
                            relative inline-flex flex-shrink-0 h-4 w-6 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus-visible:ring-2  focus-visible:ring-white focus-visible:ring-opacity-75`}
                        >
                        <span
                            aria-hidden="true"
                            className={`${this.props.getValue() ? 'translate-x-2' : 'translate-x-0'}
                            pointer-events-none inline-block h-3 w-3 rounded-full bg-white shadow-lg transform ring-0 transition ease-in-out duration-200`}
                        />
                    </Switch>
                    <span className={`${this.props.getValue()? 'text-gray-500' : ''}`}>
                        {this.props.labels.right}
                    </span>
                </div>
            </div>
        );
    }
}

export default connect(Select.mapStateToProps, {newSelectValue})(Select);