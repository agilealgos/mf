import React, {Component} from 'react';
import { connect } from "react-redux";
import { RadioGroup } from '@headlessui/react';
import classNames from 'classnames';
import RowActions from './actions/RowActions';
const {newColumn, closeNewColumnSelector} = RowActions;

class NewColumnSelector extends Component
{
    registeredColumns = Object.values(CouponsPlus.components.columns);

    columnSubtypes = [
        __('Regular', CouponsPlus.textDomain), 
        __('Offers', CouponsPlus.textDomain)
    ];

    columnsData = {
        [this.columnSubtypes[0]]: this.registeredColumns.filter(({meta}) => !meta.isOffersColumn),
        [this.columnSubtypes[1]]: this.registeredColumns.filter(({meta}) => meta.isOffersColumn)
    }

    state = {
        columnSelected: this.columnsData[this.columnSubtypes[0]][0].type,
        selectedColumnSubtype: this.columnSubtypes[0]
    };

    static mapStateToProps(state, props) {
        return props;
    };

    render() 
    {
        const CheckIcon = this.getCheckIcon();

        return (
            <div className="flex flex-col">
                <h1 className="relative text-center text-2x text-gray-550 mb-4">
                    {__('Select Column Type', CouponsPlus.textDomain)}
                    {this.props.columnTemporaryId? (
                        <button className="absolute right-0 top-[-24px] text-smaller-1 bg-gray-300 bg-gray-400 text-gray-100 h-5 px-3 rounded-full" onClick={this.props.closeNewColumnSelector}>close</button>
                    ) : ''}
                </h1>
                <div className="flex flex-row justify-between mb-4 mx-1 p-1 rounded-2 bg-gray-150">
                    {this.columnSubtypes.map(subType => (
                        <button className={classNames({
                            'flex flex-row items-center justify-center h-8 px-10 rounded-3': true,
                            'text-gray-550': subType !== this.state.selectedColumnSubtype,
                            'bg-gray-250 text-gray-600': subType === this.state.selectedColumnSubtype
                        })} onClick={() => this.setState({selectedColumnSubtype: subType})}>
                            {subType}
                        </button>
                    ))}
                </div>
                <RadioGroup value={this.state.columnSelected} className="w-68 flex flex-col" onChange={type => this.setState({
                    columnSelected: type
                })}>
                    <RadioGroup.Label className="sr-only">{__('Column Type', CouponsPlus.textDomain)}</RadioGroup.Label>
                    <div className="space-y-2 max-h-85 overflow-y-auto p-1">
                        {Object.values(this.columnsData[this.state.selectedColumnSubtype]).map(({type, meta}) => (
                          <RadioGroup.Option
                            key={type}
                            value={type}
                            className={({ active, checked }) =>
                              `${
                                active
                                  ? 'ring-4 ring-gray-400 ring-opacity-60'
                                  : ''
                              }
                              ${
                                checked ? 'bg-gray-600 bg-opacity-75 text-white ring-4 ring-gray-400 ring-opacity-60' : 'bg-gray-150'
                              }
                                relative rounded-2 px-5 py-3 cursor-pointer flex focus:outline-none`
                            }
                          >
                            {({ active, checked }) => (
                              <>
                                <div className="flex items-center justify-between w-full">
                                  <div className="flex items-center">
                                    <div className="text-sm">
                                      <RadioGroup.Label
                                        as="p"
                                        className={`font-medium text-1x ${
                                          checked ? 'text-white' : 'text-gray-900'
                                        }`}
                                      >
                                        {meta.name}
                                      </RadioGroup.Label>
                                      <RadioGroup.Description
                                        as="span"
                                        className={`inline-flex text-smaller-1 max-w-50 ${
                                          checked ? 'text-sky-100' : 'text-gray-500'
                                        }`}
                                      >
                                        <span>
                                          {meta.description}
                                        </span>
                                      </RadioGroup.Description>
                                    </div>
                                  </div>
                                  {checked && (
                                    <div className="flex-shrink-0 text-blue-normal">
                                      {CheckIcon}
                                    </div>
                                  )}
                                </div>
                              </>
                            )}
                          </RadioGroup.Option>
                        ))}
                    </div>
                </RadioGroup>
                <button className={"flex flex-row space-x-1 items-center justify-center mt-3 bg-blue-normal text-gray-100 px-12 h-8 rounded-1"} onClick={() => this.props.newColumn(
                    this.props.rowTemporaryID,
                    this.props.columnTemporaryId ?? null, // it's optional!
                    this.state.columnSelected
                )}>
                    <span className="flex h-4">{CouponsPlus.text.create.column}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                      <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 1.414L10.586 9H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z" clipRule="evenodd" />
                    </svg>
                </button>
            </div>
        );
    }

    getCheckIcon() 
    {
        return (
            <svg viewBox="0 0 24 24" fill="none" className="w-6 h-6">
              <circle cx={12} cy={12} r={12} fill="#fff" opacity="0.2" />
              <path
                d="M7 13l3 3 7-7"
                stroke="#fff"
                strokeWidth={1.5}
                strokeLinecap="round"
                strokeLinejoin="round"
              />
            </svg>
        )
    }
}

export default connect(NewColumnSelector.mapStateToProps, {newColumn,closeNewColumnSelector})(NewColumnSelector);