import React, {Component} from 'react';
import _ from 'lodash';
import ColumnsRegistrator from './columns/ColumnsRegistrator';
import { connect } from "react-redux";
import Context from './Context';
import withTemporaryId from './withTemporaryId';
import RowActions from './actions/RowActions';
import classNames from 'classnames';

const {removeColumn} = RowActions;

class Column extends Component
{
    static structure = () => withTemporaryId({
        "type": "",
        "testableType": "",
        "defaultOffers": [
        ],
        "contexts": [
        ]
    });

    state = {
        isHoveringButton: false
    };

    static map(column) 
    {
        return _.merge({}, Column.structure(), column);
    }

    static mapStateToProps(state, props) {
        return {data: Column.map(props.data), ...state}
    };

    render() 
    {
        const Column = this.getColumnComponent();

        return (
            <div className={classNames({
                'flex flex-col space-y-5 first:pl-0 min-w-70': true,
                'pr-6': this.props.isTheFirstColumn,
                'px-6': !this.props.isTheFirstColumn,
                'border-r-2 border-dashed border-gray-350': this.props.hasMoreColumnsToTheRight
            })}>
                <header className="flex flex-row justify-end items-center space-x-2">
                    <span className="text-gray-300">{Column.getMeta()?.name} {__('Column', window.CouponsPlus.textDomain)}</span>
                    <button 
                        className="bg-gray-300 text-transparent hover:bg-gray-400 hover:text-gray-100 h-5 px-3 rounded-full"
                        onMouseEnter={() => this.setState({isHoveringButton: true})}
                        onMouseLeave={() => this.setState({isHoveringButton: false})}
                        onClick={() => this.props.removeColumn(
                            this.props.rowData.temporaryID,
                            this.props.data.temporaryID
                        )}
                    >
                        {this.state.isHoveringButton? __('Remove column', CouponsPlus.textDomain) : __('Remove', CouponsPlus.textDomain)}
                    </button>
                </header>
                <Column 
                    getContextComponents={(extraContextProps) => this.getContexts().map(contextData => ([
                            (<Context 
                                key={contextData.temporaryID} 
                                data={contextData} 
                                testableType={this.props.data.testableType}
                                columnData={this.props.data}
                                hasMoreColumnsToTheRight={this.props.hasMoreColumnsToTheRight}
                                {...(extraContextProps || {})}
                            />),
                            contextData,
                        ]))
                    } 
                    columnNumber={this.props.columnNumber}
                    columnData={this.props.data}
                    hasMoreColumnsToTheRight={this.props.hasMoreColumnsToTheRight}
                    isTheFirstColumn={this.props.isTheFirstColumn}
                />
            </div>
        );
    }

    getColumnComponent() : string
    {
        return ColumnsRegistrator.getAll().find(Column => Column.TYPE === this.props.data.type);
    }

    getContexts() : array
    {
        const contexts = this.props.data.contexts;

        if (!Array.isArray(contexts)) {
            console.error(__('Row prop: rowData isnt an array (and it needs to be, fallbacking to an empty array)', CouponsPlus.textDomain));
            return [];
        }   

        return contexts;
    }
}

export default connect(Column.mapStateToProps, {removeColumn})(Column);