import React, {Component, Fragment} from 'react';
import { connect } from "react-redux";
import { Listbox, Transition } from '@headlessui/react'
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
            <div className="flex flex-col">
                {this.props.labels?.top? <div className="px-3 smaller-1 capitalize text-gray-300 mb-1">{this.props.labels?.top}</div> : ''}
                <div className="relative text-gray-650 flex flex-row space-x-1 items-center">
                    <Listbox value={this.props.getValue()} onChange={value => this.props.newSelectValue(
                        value, this.props.temporaryID, this.props.propertyName
                    )}>
                      <Listbox.Button className={`relative ${this.props.width === 'auto'? 'w-auto' : 'w-full'} space-x-1 flex flex-row justify-between items-center px-3 h-9 text-left rounded-4 border-px border-gray-200 focus:border-blue-normal focus:outline-none shadow-input cursor-default hover:cursor-pointer text-base select-none`}>
                        <span>{this.props.getValue()}</span>{this.getButtonIcon()}
                        </Listbox.Button>
                      <Transition appear as={Fragment}>
                          <Listbox.Options className="absolute top-0 py-0 overflow-auto text-base bg-white border border-gray-100 rounded-5 shadow-lg max-h-60 focus:outline-none z-50">
                              {this.getMappedOptions().map(([value, name]) => (
                              <Listbox.Option
                                key={value}
                                value={value}
                                className="mb-0 h-9 whitespace-nowrap flex flex-row items-center px-3 hover:cursor-pointer hover:bg-gray-150 select-none"
                              >
                                {name}
                              </Listbox.Option>
                              ))}
                          </Listbox.Options>
                      </Transition>
                    </Listbox>
                    {this.props.labels?.right? <div className="smaller-1 text-gray-400">{this.props.labels?.right}</div> : ''}
                </div>
                {this.props.labels?.bottom? <div className="px-3 smaller-1 text-gray-300 mb-1 mt-1">{this.props.labels?.bottom}</div> : ''}
            </div>
        );
    }

    getMappedOptions() : array
    {
        if (Array.isArray(this.props.options)) {
            return this.props.options.map(option => [option, option])
        }

        const options = [];

        _.forOwn(this.props.options, (name, value) => options.push([name, value]));

        return options;
    }
    getButtonIcon() 
    {
        return (
            <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
            </svg>
        )
    }
}

export default connect(Select.mapStateToProps, {newSelectValue})(Select);