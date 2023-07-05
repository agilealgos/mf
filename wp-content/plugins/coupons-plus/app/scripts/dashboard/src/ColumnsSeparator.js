import React, {Component} from 'react';
import { connect } from "react-redux";
import classNames from 'classnames';
import { merge } from 'lodash';

class ColumnsSeparator extends Component
{
    static mapStateToProps(state, props) {
        return merge({}, props, {newColumnSelector: state.newColumnSelector});
    };

    render() 
    {
        return (
            <div className={classNames({
                'relative h-full w-full flex flex-row space-x-1 items-start justify-between': true,
                'pt-12': !this.props.disableNumeration
            })}>
                {this.isSingleColumn()? (
                    <div className="relative w-full flex flex-col items-center">
                        {this.getStep({context: 'leftColumn'})}
                        {this.props.leftColumn()}
                        {!this.props.hideArrowOnSingleColumn? this.getRightArrow({context: 'singleColumn'}) : ''}
                    </div>
                ): (
                    <div className="relative flex flex-col space-x-1">
                        {this.getStep({context: 'leftColumn'})}
                        {this.props.leftColumn()}
                        {this.getRightArrow({context: 'doubleColumn'})}
                    </div>
                )}
                {!this.isSingleColumn() || this.props.newColumnSelector.isOpen ? (
                    <div className="w-px h-full border-l-2 border-dashed border-gray-350"></div>
                ) : ''}
                {this.isSingleColumn()? '' : (
                    <div className="relative flex flex-col items-center px-4">
                        {this.getStep({context: 'rightColumn'})}
                        {this.props.rightColumn()}
                        {/*this.getRightArrow({context: 'right'})*/}
                    </div>
                )}
            </div>
        );
    }

    isSingleColumn() 
    {
        return this.props.singleColumn || this.props.newColumnSelector.isOpen;
    }

    getStep({context}) 
    {
        return this.props.disableNumeration? '' : (
            <div className="absolute top-[-64px] w-full h-10 flex flex-row items-center justify-center">
                <div className="flex flex-row items-center justify-center w-8 h-8 bg-gray-400 rounded-full text-gray-100">
                    {context === 'leftColumn'? this.props.columnNumber : this.props.columnNumber + 1}
                </div>
            </div>
        )
    }

    getRightArrow({context}) 
    {
        return (
            <>
                <div className={classNames({
                    'absolute top-[50%] translate-y-[-50%] h-[2px] bg-gray-300': true,
                    'right-[-14px] w-6': context === 'doubleColumn',
                    'right-[-28px] w-5': context === 'singleColumn'
                })}></div>
                <svg xmlns="http://www.w3.org/2000/svg" className={classNames({
                    'absolute top-[50%] translate-y-[-50%] h-5 text-gray-300': true,
                    'right-[-24px] w-5': context === 'doubleColumn',
                    'right-[-46px] w-5': context === 'singleColumn'
                })} viewBox="0 0 20 20" fill="currentColor">
                  <path fillRule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clipRule="evenodd" />
                </svg>
            </>
        )
    }

    alternativeColumnsSeparator() 
    {
        return (
            <div className="relative flex flex-row space-x-5 items-center">
                {this.isSingleColumn()? this.props.leftColumn(): (
                    <div className="relative flex flex-row space-x-1">
                        {this.props.leftColumn()}
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
                )}
                {this.isSingleColumn()? '' : (
                    <div className="relative flex flex-col items-center pl-9">
                        {this.props.rightColumn()}
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
                )}
            </div>
        );
    }
}

export default connect(ColumnsSeparator.mapStateToProps, {})(ColumnsSeparator);