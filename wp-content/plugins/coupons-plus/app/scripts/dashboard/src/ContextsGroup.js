import React, {Component} from 'react';
import { connect } from "react-redux";
import ColumnActions from './actions/ColumnActions';
import ColumnsSeparator from './ColumnsSeparator';
import OffersSet from './OffersSet';
import RightColumnOptions from './RightColumnOptions';
import classNames from 'classnames';

const {newContext, removeContext} = ColumnActions;

class ContextsGroup extends Component
{
    static mapStateToProps(state, props) {
        return props;
    };

    render() 
    {
        
        return (
            <div className={classNames({
                'flex flex-col items-center space-y-4': true,
                'pr-6': !this.props.hasMoreColumnsToTheRight
            })}>
                {this.props.contextComponents.map(([ContextComponent, contextData]) => (
                        <>
                            <ColumnsSeparator
                                disableNumeration={this.props.disableNumeration}
                                columnNumber={this.props.columnNumber}
                                isTheFirstColumn={this.props.isTheFirstColumn}
                                singleColumn={!this.props.individualContextSelector}
                                hideArrowOnSingleColumn={!this.props.individualContextSelector}
                                leftColumn={() => (
                                    <div className={classNames({
                                            'relative p-1 rounded-3 bg-gray-150 bg-opacity-20 w-full': true,
                                            'border-2 border-gray-300': false, //this design is currently being revised
                                            'mr-4': !this.props.hasMoreColumnsToTheRight && !this.props.disableColumnsSeparatorMarginRight
                                        })}>
                                        <button className="absolute top-[-12px] right-[-12px] bg-gray-250 rounded-full group" onClick={() => this.props.removeContext(
                                                this.props.columnData.temporaryID,
                                                contextData.temporaryID
                                            )}>
                                            <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6 opacity-0 group-hover:opacity-100" viewBox="0 0 20 20" fill="currentColor">
                                              <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clipRule="evenodd" />
                                            </svg>
                                        </button>
                                        {ContextComponent}
                                    </div>
                                )}
                                rightColumn={() => {
                                    return contextData.offers.length ? (
                                        <OffersSet offersSetRelation="context"
                                                   offersData={contextData.offers} 
                                                   contextId={contextData.temporaryID} 
                                                   columnId={this.props.columnData.temporaryID} />
                                    ) : (
                                        <RightColumnOptions 
                                            newColumnConfirmationMessage={this.props.newColumnConfirmationMessage}
                                            offersSetRelation="context"
                                            columnTemporaryId={this.props.columnData.temporaryID}
                                            contextTemporaryID={contextData.temporaryID}
                                            offersData={contextData.offers} 
                                        />
                                    )
                                }}
                            />
                            {this.props.afterGroup?.()}
                        </>
                ))}
                <button className="space-x-1 items-center justify-center mt-2 flex flex-row py-1 text-gray-400" onClick={() => this.props.newContext(this.props.columnData.temporaryID)}>
                    <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                      <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clipRule="evenodd" />
                    </svg>
                    <span>{CouponsPlus.text.add.context}</span>
                </button>
            </div>
        );
    }

    showContext() 
    {
        return (
            <div className="flex flex-col items-center space-y-4">
                {this.props.contextComponents.map(([ContextComponent, contextData]) => (
                        <>
                            <div className="relative p-1 rounded-3 bg-gray-150 border-2 border-gray-300 w-full">
                                <button className="absolute top-[-12px] right-[-12px] bg-gray-250 rounded-full group" onClick={() => this.props.removeContext(
                                        this.props.columnData.temporaryID,
                                        contextData.temporaryID
                                    )}>
                                    <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6 opacity-0 group-hover:opacity-100" viewBox="0 0 20 20" fill="currentColor">
                                      <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clipRule="evenodd" />
                                    </svg>
                                </button>
                                {ContextComponent}
                            </div>
                            {this.props.afterGroup?.()}
                        </>
                ))}
                <button className="space-x-1 items-center justify-center mt-2 flex flex-row py-1 text-gray-400" onClick={() => this.props.newContext(this.props.columnData.temporaryID)}>
                    <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                      <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clipRule="evenodd" />
                    </svg>
                    <span>{CouponsPlus.text.add.context}</span>
                </button>
            </div>
        )
    }
}

export default connect(ContextsGroup.mapStateToProps, {newContext, removeContext})(ContextsGroup);