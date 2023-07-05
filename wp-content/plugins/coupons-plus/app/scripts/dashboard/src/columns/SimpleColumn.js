import React, {Component} from 'react';
import ColumnsSeparator from '../ColumnsSeparator';
import RightColumnOptions from '../RightColumnOptions';
import OffersSet from '../OffersSet';

export default class SimpleColumn extends Component
{
    static structure = {};

    static TYPE = CouponsPlus.components.columns.Simple.type;

    static getMeta() 
    {
        return CouponsPlus.components.columns.Simple.meta;
    }
 
    render() 
    {
        return this.props.getContextComponents().map(([Context, contextData]) => (
            <ColumnsSeparator
                key={this.props.columnData.temporaryID}
                columnNumber={this.props.columnNumber}
                singleColumn={this.props.hasMoreColumnsToTheRight}
                isTheFirstColumn={this.props.isTheFirstColumn}
                alignCenter={!this.props.columnData.defaultOffers.length}
                leftColumn={() => Context}
                rightColumn={() => {
                    return this.props.columnData.defaultOffers.length ? (
                        <OffersSet offersSetRelation="column"
                                   offersData={this.props.columnData.defaultOffers} 
                                   contextId={contextData.temporaryID} 
                                   columnId={this.props.columnData.temporaryID} />
                    ) : (
                        <RightColumnOptions 
                            offersSetRelation="column"
                            columnTemporaryId={this.props.columnData.temporaryID}
                            contextTemporaryID={contextData.temporaryID}
                            offersData={this.props.columnData.defaultOffers}
                            hasMoreColumnsToTheRight={this.props.hasMoreColumnsToTheRight}
                        />
                    )
                }}
            />
        ));
    }
}