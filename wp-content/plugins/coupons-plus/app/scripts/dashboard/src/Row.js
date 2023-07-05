import React, {Component} from 'react';
import { connect } from "react-redux";
import Column from './Column.js';
import shortid from 'shortid';
import { merge } from 'lodash';
import withTemporaryId from './withTemporaryId';
import NewColumnSelector from './NewColumnSelector';
import classNames from 'classnames';
import RowsActions from 'actions/RowsActions';

class Row extends Component
{
    static structure = () => withTemporaryId({
        "temporaryID": shortid.generate(),
        "columns": []
    });

    static mapStateToProps(state, props) {
        return merge({data: {}}, {data: Row.map(props.data)}, {newColumnSelector: state.newColumnSelector})
    };

    static map(row) 
    {
        return merge({}, Row.structure(), row);
    }

    render() 
    {
        return (
            <div className="relative flex flex-row w-full">
                <div className="flex flex-col justify-center border-r-px border-gray-300 pr-2 mr-2">
                    <div className="relative flex flex-col space-y-3 self-center">
                        <div className="flex items-center justify-center text-1x uppercase rounded-4 h-6 w-6 bg-gray-350 text-gray-100">
                            {this.props.rowLetter}
                        </div>
                        <button onClick={() => this.props.removeRow(this.props.data.temporaryID)} className="rounded-full bg-gray-150 text-gray-300 h-6 w-6 flex items-center justify-center cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                              <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clipRule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
                {!this.getColumns().length? (
                   <NewColumnSelector rowTemporaryID={this.props.data.temporaryID}/>
                ): ''}
                {this.getColumns().map((columnData, columnIndex, columns) => (
                    <React.Fragment key={columnData.temporaryID}>
                        <Column
                                data={columnData} 
                                rowData={this.props.data} 
                                hasMoreColumnsToTheRight={(columnIndex + 1) !== columns.length}
                                isTheFirstColumn={columnIndex === 0}
                                columnNumber={columnIndex + 1}
                        />
                        {(this.props.newColumnSelector.isOpen && this.props.newColumnSelector.columnTemporaryId === columnData.temporaryID)? (
                            <NewColumnSelector 
                                rowTemporaryID={this.props.data.temporaryID}
                                columnTemporaryId={columnData.temporaryID}
                            />
                        ) : ''}
                    </React.Fragment>
                ))}
            </div>
        );
    }

    getColumns() : array
    {
        const columns = this.props.data.columns;

        if (!Array.isArray(columns)) {
            console.error('Row prop: rowData isnt an array (and it needs to be, fallbacking to an empty array)');
            return [];
        }   

        return columns;
    }

    getNewConditionsColumn() 
    {
        return (<NewColumnSelector rowTemporaryID={this.props.data.temporaryID}/>)
    }
}

export default connect(Row.mapStateToProps, {
    removeRow: RowsActions.removeRow
})(Row);