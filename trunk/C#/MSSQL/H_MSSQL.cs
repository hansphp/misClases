using System;
using System.Collections.Generic;
using System.Linq;
using System.Data;
using System.Web;
using System.Web.UI;
using System.Web.UI.WebControls;
using System.Web.UI.HtmlControls;
using System.Data.SqlClient;
using System.Configuration;

/* ///////////Developed By
  _   _       ___   __   _   _____        _     _   _____   __   _  
 | | | |     /   | |  \ | | /  ___/      | |   / / /  _  \ |  \ | | 
 | |_| |    / /| | |   \| | | |___       | |  / /  | | | | |   \| | 
 |  _  |   / / | | | |\   | \___  \      | | / /   | | | | | |\   | 
 | | | |  / /  | | | | \  |  ___| |      | |/ /    | |_| | | | \  | 
 |_| |_| /_/   |_| |_|  \_| /_____/      |___/     \_____/ |_|  \_| 
/// <summary>
///     Manejador de Base de Datos para SQL Server (Edición para desarrollo).
/// </summary>
///////////////////////////////////////////////////////////////////
//  //       Autor : Hans Von Herrera Ortega
//  //     Versión : 1.0 Dev.
//  //      Bugs a : hans.php@gmail.com */
public class H_MSSQL
{
    protected string name;
    protected SqlConnection conexión;
    protected SqlDataReader reader;
    protected SqlCommand command;
    protected string error;
    protected bool conectado;
    protected string sql;
    protected Panel panel;
    protected bool debug = false;
    protected HtmlTable debugTable;

    /* Construcción del objeto */
    public H_MSSQL(string name)
    {
        try
        {
            this.name = name;
            conexión = new SqlConnection(ConfigurationManager.ConnectionStrings[this.name].ConnectionString);
            conexión.Open();
            conectado = true;
        }
        catch
        {
            this.name = string.Empty;
            conectado = false;
            error = "Error de conexión a la base de datos";
        }
    }

    public SqlDataReader fila(string sql)
    {
        this.sql = sql;
        command = new SqlCommand(this.sql, conexión);
        reader = command.ExecuteReader();

        if (debug)
        {
            debugRun();
            return null;
        }

        if (reader.Read())
            return reader;
        else
            return null;
    }
    public int insert(string sql)
    {
        int affectedRows = 0;
        this.sql = sql;
        command = new SqlCommand(this.sql, conexión);
        affectedRows = command.ExecuteNonQuery();

        if (debug)
        {
            debugRun();
            return 0;
        }
        return affectedRows;
    }

    public int update(string sql)
    {
        int affectedRows = 0;
        this.sql = sql;
        command = new SqlCommand(this.sql, conexión);
        affectedRows = command.ExecuteNonQuery();

        if (debug)
        {
            debugRun();
            return 0;
        }
        return affectedRows;
    }

    ~H_MSSQL()
    {
        conexión.Close();
    }

    /********** Métodos para depuración */
    public void debugOn(Panel panel)
    {
        this.panel = panel;
        this.debug = true;
        this.debugTable = new HtmlTable();
    }
    protected void debugRun()
    {
        debugTable.Attributes.Add("class", "debugTable");
        var row = new HtmlTableRow();
        row.Attributes.Add("class", "debugHeader");
        foreach (DataRow campo in reader.GetSchemaTable().Rows)
        {
            row.Cells.Add(new HtmlTableCell("td")
            {
                InnerHtml = "<label>" + campo["ColumnName"] + "</label> [" + campo["DataTypeName"] + " (<b>" + campo["ColumnSize"] + "</b>)]"
            });
        }

        debugTable.Rows.Add(row);
        while (reader.Read())
        {
            readRow(((IDataRecord)reader));
        }

        debugTable.Controls.Add(new HtmlTableRow());
        debugTable.ID = "dynamictextbox";
        panel.Controls.Add(debugTable);
    }
    protected void readRow(IDataRecord sd)
    {
        var row = new HtmlTableRow();
        for (int i = 0; i < reader.FieldCount; i++)
        {
            row.Cells.Add(new HtmlTableCell("td") { InnerText = sd[i].ToString() });
        }
        debugTable.Rows.Add(row);
    }
}