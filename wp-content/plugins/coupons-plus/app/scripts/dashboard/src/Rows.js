import React, {Component} from 'react';
import Row from './Row.js';
import RowsActions from './actions/RowsActions';
import { connect } from "react-redux";

class Rows extends Component
{
    static structure = {
        'rows': []
    };

    static mapStateToProps(state){
        return {
            rows: state.rows || [],
        }
    };

    render() 
    {
        //<button onClick={() => this.props.addRow()}></button>
        return (
            <div className="flex flex-col items-center space-y-1">
                {this.noRows()}
                {this.props.rows.map((rowData, index) => (
                    <div key={rowData.temporaryID} className="flex flex-col w-full">
                        <Row data={rowData} rowLetter={String.fromCharCode(97 + index)}/>
                        <div className="w-full text-gray-400 flex flex-row justify-between items-center">

                        <div className="w-full h-px border-t-2 border-dashed border-gray-400"></div> 
                        <span className="flex text-gray-150 leading-5 px-2 rounded-3 bg-gray-400">{__('OR', CouponsPlus.textDomain)}</span> 
                        <div className="w-full h-px border-t-2 border-dashed border-gray-400"></div>
                    </div>
                        <button className={(
                            'space-x-1 items-center justify-center mt-2 flex flex-row py-1 text-gray-400'
                        )} onClick={() => this.props.newRow(
                            rowData.temporaryID
                        )}>
                            <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                              <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clipRule="evenodd" />
                            </svg>
                            <span>{CouponsPlus.text.add.rows}</span>
                        </button>    
                    </div>
                ))}
            </div>
        );
    }

    noRows() 
    {
        return (
            <>
                {!this.props.rows.length? (
                    <div className="flex flex-col justify-center items-center min-h-80 text-gray-600">
                        <img src={CouponsPlus.urls.icons.noRows} className="w-70 border-none" alt={__('No Rows!', CouponsPlus.textDomain)}/>
                        <h1 className="text-4x text-current mt-10">{__('Nothing here, yet!', CouponsPlus.textDomain)}</h1>
                        <div className="flex flex-col items-center mt-4 text-gray-500">
                            <p className="text-base max-w-90 text-center">{__('Coupons+ is made of:', CouponsPlus.textDomain)}</p>  
                            <p className="flex flex-row space-x-1 text-base text-center mt-1">
                                <span>
                                    {__('Rows', CouponsPlus.textDomain)}
                                </span>
                                <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                  <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 1.414L10.586 9H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z" clipRule="evenodd" />
                                </svg>
                                <span>
                                    {__('Columns', CouponsPlus.textDomain)}
                                </span>
                                <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                  <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 1.414L10.586 9H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z" clipRule="evenodd" />
                                </svg>
                                <span>
                                    {__('Conditions/Filters', CouponsPlus.textDomain)}
                                </span>
                                <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                  <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 1.414L10.586 9H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z" clipRule="evenodd" />
                                </svg>
                                <span>
                                    {__('Offers', CouponsPlus.textDomain)}
                                </span>
                            </p>  
                            <p className="text-base max-w-90 text-center mt-9">{__('Add a new row or select a preset to get started.', CouponsPlus.textDomain)}</p>  
                            <button className={"flex flex-row space-x-1 items-center justify-center mt-3 bg-blue-normal text-gray-100 px-12 h-8 rounded-1"} onClick={() => this.props.newRow()}>
                                <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                  <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clipRule="evenodd" />
                                </svg>
                                <span className="flex h-4">{CouponsPlus.text.add.rows}</span>
                            </button>
                        </div>
                    </div>
                ): ''}
            </>
        );
    }
}
export default connect(Rows.mapStateToProps, RowsActions)(Rows);