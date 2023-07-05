import React, {Component} from 'react';
import { connect } from "react-redux";
import { merge } from 'lodash';
import Testable from './Testable';
import withTemporaryId from './withTemporaryId';
import {conditions, filters} from './conditionsOrFilters/CardCreator';
import ContextActions from './actions/ContextActions';
import Tippy from '@tippyjs/react/headless'; 
import classNames from 'classnames';
import ColumnsSeparator from './ColumnsSeparator';
import Offer from './conditionsOrFilters/Offer';

const {newConditionOrFilter} = ContextActions;


class Context extends Component
{
    state = {
        panelIsOpen: false
    };

    static structure = () => withTemporaryId({
        "conditionsOrFilters": [],
        "offers": []
    });

    static map(context) 
    {
        const newContext =  merge({}, Context.structure(), context);
        newContext.offers = context.offers.map(offer => Offer.map(offer))

        return newContext;
    }

    static mapStateToProps(state, props) {
        return merge({data: {}}, {data: Context.map(props.data)})
    };

    render() 
    {
        return (
            <div className={classNames({
                'flex flex-col space-y-4 justify-center items-center': true,
                'pr-4': !this.props.hasMoreColumnsToTheRight && !this.props.disableMarginRight
            })}>
                {!this.hasConditionsOrFilters()? (
                    <div className="relative flex flex-col justify-center items-center space-y-3 text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" className="h-9 w-9" viewBox="0 0 20 20" fill="currentColor">
                          <path d="M10 3.5a1.5 1.5 0 013 0V4a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-.5a1.5 1.5 0 000 3h.5a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-.5a1.5 1.5 0 00-3 0v.5a1 1 0 01-1 1H6a1 1 0 01-1-1v-3a1 1 0 00-1-1h-.5a1.5 1.5 0 010-3H4a1 1 0 001-1V6a1 1 0 011-1h3a1 1 0 001-1v-.5z" />
                        </svg>
                        <h1 className="text-2x text-gray-400">{__('No Conditions or Filters!', CouponsPlus.textDomain)}</h1>
                    </div>
                ): ''}
                {this.props.data.conditionsOrFilters.map(conditionOrFilter => {
                    return <Testable 
                                key={conditionOrFilter.temporaryID} 
                                conditionOrFilter={conditionOrFilter} 
                                testableType={this.props.testableType}
                                contextTemporaryID={this.props.data.temporaryID}
                                columnTemporaryID={this.props.columnData.temporaryID}
                            />;
                })}
                {this.getPopOver()}
            </div>
        );
        /*
        return (
            <div className="relative flex flex-row space-x-5 items-center">
                <div className="relative flex flex-row space-x-1">
                    <div className="flex flex-col space-y-4 pr-8 justify-center items-center">
                        {!this.hasConditionsOrFilters()? (
                            <div className="relative flex flex-col justify-center items-center space-y-3 text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" className="h-9 w-9" viewBox="0 0 20 20" fill="currentColor">
                                  <path d="M10 3.5a1.5 1.5 0 013 0V4a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-.5a1.5 1.5 0 000 3h.5a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-.5a1.5 1.5 0 00-3 0v.5a1 1 0 01-1 1H6a1 1 0 01-1-1v-3a1 1 0 00-1-1h-.5a1.5 1.5 0 010-3H4a1 1 0 001-1V6a1 1 0 011-1h3a1 1 0 001-1v-.5z" />
                                </svg>
                                <h1 className="text-2x text-gray-400">{__('No Conditions or Filters!', CouponsPlus.textDomain)}</h1>
                            </div>
                        ): ''}
                        {this.props.data.conditionsOrFilters.map(conditionOrFilter => {
                            return <Testable 
                                        key={conditionOrFilter.temporaryID} 
                                        conditionOrFilter={conditionOrFilter} 
                                        testableType={this.props.testableType}
                                        contextTemporaryID={this.props.data.temporaryID}
                                        columnTemporaryID={this.props.columnData.temporaryID}
                                    />;
                        })}
                        {this.getPopOver()}
                    </div>
                    <div className="absolute right-0 h-full w-4
                                    border-gray-300
                                    border-l-0
                                    border-r-[2px]
                                    border-t-[2px]
                                    border-b-[2px]
                                    rounded-tl-0
                                    rounded-bl-0
                                    rounded-3
                                    "></div>
                </div>
                <div className="relative flex flex-col items-center pl-9">
                    <div className="flex flex-col">
                        <button className="space-x-1 items-center justify-center mt-[-8px] inline-flex bg-blue-normal text-gray-100 px-8 h-8 rounded-1" onClick={() => this.setState({panelIsOpen: true})}>
                            <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                              <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clipRule="evenodd" />
                            </svg>
                            <span>add offers</span>
                        </button>
                        <button className="space-x-1 items-center justify-center mt-3 mb-[-8px] inline-flex bg-gray-300 text-gray-550 px-8 h-8 rounded-1" onClick={() => this.setState({panelIsOpen: true})}>
                            <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                              <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clipRule="evenodd" />
                            </svg>
                            <span>add column</span>
                        </button>
                    </div>
                    <div className="absolute top-[50%] translate-y-[-50%] left-[-20px] w-5 h-[2px] bg-gray-300"></div>
                    <div className="absolute left-0 h-full w-4
                                    border-gray-300
                                    text-gray-300
                                    border-r-0
                                    border-l-[2px]
                                    border-t-[2px]
                                    border-b-[2px]
                                    rounded-tr-0
                                    rounded-br-0
                                    rounded-4
                                    ">
                        <svg xmlns="http://www.w3.org/2000/svg" className="absolute h-5 w-5 right-[-18px] top-[-11px]" viewBox="0 0 20 20" fill="currentColor">
                          <path fillRule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clipRule="evenodd" />
                        </svg>
                        <svg xmlns="http://www.w3.org/2000/svg" className="absolute h-5 w-5 right-[-18px] bottom-[-11px]" viewBox="0 0 20 20" fill="currentColor">
                          <path fillRule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clipRule="evenodd" />
                        </svg>
                    </div>
                </div>
            </div>
        );*/
    }

    getPopOver() : object
    {
        return (
            <Tippy
                allowHTML={true}
                followCursor={true}
                placement="auto-end"
                interactive={true}
                visible={this.state.panelIsOpen}
                onClickOutside={() => this.setState({panelIsOpen: false})}
                render={attrs => (
                <div className={classNames(
                    'z-1000 px-4 sm:px-0',
                    {
                        'w-[900px]': this.props.testableType !== 'filters',
                        'w-[600px]': this.props.testableType === 'filters'
                    }
                )} {...attrs}>
                  <div className="overflow-hidden rounded-2 shadow-lg bg-gray-150">
                        <div className="flex flex-row space-x-1">
                            <div className="flex flex-col w-[66%]">
                                <h1 className="text-2x text-gray-500 px-4 py-3">{__('Conditions', CouponsPlus.textDomain)}</h1>
                                <div className={classNames(
                                    'relative w-full h-full grid gap-4 bg-white p-4',
                                    {
                                        'grid-cols-2': this.props.testableType !== 'filters',
                                        'grid-cols-1': this.props.testableType === 'filters'
                                    }
                                )}>
                                    {this.props.testableType === 'filters'? (
                                        <div className="flex flex-col w-full px-9">
                                            <h1 className="flex flex-col text-gray-600 text-2x mb-3 space-y-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                                </svg>
                                                <span>{__('Select a Filter', CouponsPlus.textDomain)}</span>
                                            </h1>
                                            <div className="space-y-1">
                                                <p>{__('A column may only have conditions or filters but not both', CouponsPlus.textDomain)}</p>
                                                <p>{__('If you want to add a condition, create a new column to the right', CouponsPlus.textDomain)}</p>
                                            </div>
                                        </div>
                                    ) : (conditions.map((Condition) => {
                                        const condition = new Condition({});
                                        const conditionCanBeUsed = !this.props
                                                                       .data
                                                                       .conditionsOrFilters
                                                                       .find(conditionOrFilterData => {
                                                                            return conditionOrFilterData.type === Condition.TYPE;
                                                                       });
                                        return (
                                            <button
                                            key={Condition.TYPE}
                                            disabled={conditionCanBeUsed? false : true}
                                            onClick={this.handleConditionOrFilterClick.bind(this, condition)}
                                            className={classNames(
                                                "flex items-center p-2 -m-3 transition duration-150 ease-in-out rounded-lg hover:bg-gray-50 focus:outline-none focus-visible:ring focus-visible:ring-orange-500 focus-visible:ring-opacity-50",
                                                {
                                                    'opacity-30': !conditionCanBeUsed
                                                }
                                            )}
                                          >
                                            <div className="flex items-center justify-center flex-shrink-0 rounded-3 w-10 h-10 bg-gray-300 text-white ">
                                                {condition.getIcon()}
                                            </div>
                                            <div className="flex flex-col items-start ml-4">
                                              <p className="text-sm font-medium text-gray-900">
                                                {condition.getTitle()}
                                              </p>
                                              <p className="text-sm text-gray-500 text-left">
                                                {condition.getDescription && condition.getDescription() || ''}
                                              </p>
                                            </div>
                                          </button>
                                        )
                                    }))}
                                </div>
                            </div>
                            <div className="flex flex-col w-[44%]">
                                <h1 className="text-2x text-gray-500 px-4 py-3">{__('Filters', CouponsPlus.textDomain)}</h1>
                                <div className="relative w-full h-full grid gap-4 bg-white p-4 grid-cols-1">
                                    {this.props.testableType === 'conditions'? (
                                        <div className="flex flex-col w-full px-9">
                                            <h1 className="flex flex-col text-gray-600 text-2x mb-3 space-y-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                                </svg>
                                                <span>{__('Select a Condition', CouponsPlus.textDomain)}</span>
                                            </h1>
                                            <div className="space-y-1">
                                                <p>{__('A column may only have conditions or filters but not both', CouponsPlus.textDomain)}</p>
                                                <p>{__('If you want to add a filter, create a new column to the right', CouponsPlus.textDomain)}</p>
                                            </div>
                                        </div>
                                    ) : filters.filter(Filter => {
                                        // Combined cost Of Items is deprecated
                                        // so we aint wan show it here
                                        return Filter.TYPE !== CouponsPlus.components.filters.CombinedCostOfItems.type;
                                    }).map((Filter) => {
                                        const filter = new Filter({});
                                        const filterCanBeUsed = !this.props
                                                                       .data
                                                                       .conditionsOrFilters
                                                                       .find(conditionOrFilterData => {
                                                                            return conditionOrFilterData.type === Filter.TYPE;
                                                                       });
                                        return (
                                            <button
                                            key={Filter.TYPE}
                                            disabled={filterCanBeUsed? false : true}
                                            onClick={this.handleConditionOrFilterClick.bind(this, filter)}
                                            className={classNames(
                                                "flex items-center p-2 -m-3 transition duration-150 ease-in-out rounded-lg hover:bg-gray-50 focus:outline-none focus-visible:ring focus-visible:ring-orange-500 focus-visible:ring-opacity-50",
                                                {
                                                    'opacity-30': !filterCanBeUsed
                                                }
                                            )}
                                          >
                                            <div className="flex items-center justify-center flex-shrink-0 rounded-3 w-10 h-10 bg-gray-300 text-white ">
                                                {filter.getIcon()}
                                            </div>
                                            <div className="flex flex-col items-start ml-4">
                                              <p className="text-sm font-medium text-gray-900">
                                                {filter.getTitle()}
                                              </p>
                                              <p className="text-sm text-gray-500 text-left">
                                                {filter.getDescription && filter.getDescription() || ''}
                                              </p>
                                            </div>
                                          </button>
                                        )
                                    })}
                                </div>
                            </div>
                        </div>
                      <div className="p-4 bg-gray-50">
                        <a
                          href="https://couponspluspro.com/d/conditions-and-filters"
                          target="_blank"
                          className="flow-root px-2 py-2 transition duration-150 ease-in-out rounded-md hover:bg-gray-100 focus:outline-none focus-visible:ring focus-visible:ring-orange-500 focus-visible:ring-opacity-50"
                        >
                          <span className="flex items-center">
                            <span className="text-sm font-medium text-gray-900">
                              {__('Documentation', CouponsPlus.textDomain)}
                            </span>
                          </span>
                          <span className="block text-sm text-gray-500">
                            {__('Read the guide on using conditions and filters to unlock the power of Coupons+', window.CouponsPlus.textDomain)}.
                          </span>
                        </a>
                      </div>
                    </div>
                </div>
                )}
              >
                <button className={classNames(
                    'space-x-1 items-center justify-center mt-2',
                    {
                        'flex flex-row py-1 text-gray-400': this.hasConditionsOrFilters(),
                        'inline-flex bg-blue-normal text-gray-100 px-8 h-8 rounded-1': !this.hasConditionsOrFilters()
                    })
                } onClick={() => this.setState({panelIsOpen: true})}>
                    <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                      <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clipRule="evenodd" />
                    </svg>
                    <span>{this.props.testableType? CouponsPlus.text.add[this.props.testableType] : CouponsPlus.text.add.conditionOrFilter}</span>
                </button>
              </Tippy>
        );
    }

    handleConditionOrFilterClick(conditionOrFilter: object) 
    {
        this.props.newConditionOrFilter(
            conditionOrFilter.constructor.TYPE,
            this.props.data.temporaryID,
            this.props.columnData.temporaryID
        )

        this.setState({panelIsOpen: false})
    }

    hasConditionsOrFilters() : boolean
    {
        return this.props.data.conditionsOrFilters.length;
    }
}

export default connect(Context.mapStateToProps, {newConditionOrFilter})(Context);